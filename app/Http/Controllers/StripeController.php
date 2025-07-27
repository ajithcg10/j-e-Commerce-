<?php

namespace App\Http\Controllers;

use App\CartService;
use App\Http\Resources\OrderViewResources;
use App\Mail\CheckOutCompeleted;
use App\Mail\NewOrderMail;
use App\Models\CartItem;
use App\Models\Order;
use App\OrderStatusEnum;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Mail;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function success(Request $request)
    {
        $user = Auth::user();
        
        $sessionId = $request->get('session_id');
        $orders = Order::where('stripe_session_id', $sessionId)
            ->where('user_id', $user->id)->get();
   
        if($orders->count() === 0){
            abort(404, 'Order not found');
        }

        foreach ($orders as $order){
            // dd($order);
            if($order->user_id !== $user->id){
                abort(403, 'Unauthorized access to this order');
            }
        }
        return Inertia::render('Stripe/Success', [
            'orders' => OrderViewResources::collection($orders)->collection->toArray()
        ]);
    }

    public function failure()
    {
           return Inertia::render('Stripe/Failure', [
        'message' => 'Your payment could not be completed. Please try again or contact support.',
    ]);
    }

    public function webhook(Request $request ,CartService $cartService)
    {
        $stripe = new StripeClient(config('app.stripe_secret'));
        $endpointSecret = config('app.stripe_webhook_secret');

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        Log::info("Stripe webhook received", ['type' => $event->type]);

        try {
            switch ($event->type) {

                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $paymentIntentId = $session['payment_intent'];
                    $sessionId = $session['id'];

                    $orders = Order::with('orderItems')
                        ->where('stripe_session_id', $sessionId)
                        ->get();

                    if ($orders->isEmpty()) {
                        Log::warning("No orders found for session ID: $sessionId");
                        break;
                    }

                    $productsToDeleteFromCart = [];

                    foreach ($orders as $order) {
                        $order->payment_intent = $paymentIntentId;
                        $order->status = OrderStatusEnum::Paid->value;
                        $order->save();

                        $productsToDeleteFromCart = array_merge(
                            $productsToDeleteFromCart,
                            $order->orderItems->pluck('product_id')->toArray()
                        );

                        foreach ($order->orderItems as $orderItem) {
                            $options = is_string($orderItem->variation_type_option_ids)
                                ? json_decode($orderItem->variation_type_option_ids, true)
                                : $orderItem->variation_type_option_ids;

                            $product = $orderItem->product;
                          

                            if (is_array($options)) {
                                sort($options);
                                $variation = $product->variations()
                                    ->where('variation_type_option_ids', $options)
                                    ->first();

                                if ($variation && $variation->quantity !== null) {
                                    $variation->quantity -= $orderItem->quantity;
                                    $variation->save();
                                }
                            } elseif ($product && $product->quantity !== null) {
                                $product->quantity -= $orderItem->quantity;
                                $product->save();
                            }
                        }

                        CartItem::where('user_id', $order->user_id)
                            ->whereIn('product_id', $productsToDeleteFromCart)
                            ->where('save_for_later', false)
                            ->delete();
                    }
                    break;

                case 'charge.updated':
                    $charge = $event->data->object;
                    $transactionId = $charge['balance_transaction'] ?? null;
                    $paymentIntentId = $charge['payment_intent'] ?? null;

                    if (!$transactionId || !$paymentIntentId) {
                        Log::warning("Missing transaction ID or payment intent in charge.updated");
                        break;
                    }

                    $balanceTransaction = $stripe->balanceTransactions->retrieve($transactionId);
                    $orders = Order::where('payment_intent', $paymentIntentId)->get();

                    if ($orders->isEmpty()) {
                        Log::warning("No orders found for payment intent: $paymentIntentId");
                        break;
                    }

                    $totalAmount = $balanceTransaction['amount'];
                    $stripeFee = 0;

                    foreach ($balanceTransaction['fee_details'] as $feeDetail) {
                        if ($feeDetail['type'] === 'stripe_fee') {
                            $stripeFee = $feeDetail['amount'];
                        }
                    }

                    $platformFeePercentage = config('app.platform_fee_percentage', 10);

                    foreach ($orders as $order) {
                        $vendorShare = $order->total_price / $totalAmount;
                        $order->online_payment_commission = $stripeFee * $vendorShare;
                        $order->website_commission = ($order->total_price - $order->online_payment_commission) * $platformFeePercentage / 100;
                        $order->vendor_subtotal = $order->total_price - $order->online_payment_commission - $order->website_commission;
                        $order->save();

                        Mail::to($order->vendorUser)->send(new NewOrderMail($order));
                       
                    }
                    dd( Mail::to($orders[0]->user)->send(new CheckOutCompeleted($orders)));
                    break;

                default:
                    Log::info("Unhandled Stripe event type: " . $event->type);
                    break;
            }

        } catch (\Exception $e) {
            Log::error("Error processing Stripe webhook", ['error' => $e->getMessage()]);
            return response('Internal error', 500);
        }

        return response('Webhook Handled', 200);
    }

    public function connect()
{
    $user = Auth::user();

    if (!$user->getStripeAccountId()) {
        // Do not pass any type — defaults to 'standard'
      $user->createStripeAccount(['type' => 'standard']);

    }

    if (!$user->isStripeAccountActive()) {
        return redirect($user->getStripeAccountLink());
    }

    return back()->with('success', 'Your Stripe account is already connected.');
}


}
