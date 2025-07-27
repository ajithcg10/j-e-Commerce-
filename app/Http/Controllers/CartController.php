<?php

namespace App\Http\Controllers;

use App\CartService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\OrderStatusEnum;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CartController extends Controller
{
  public function index(CartService $CartServices){
     return Inertia::render('Cart/Index',[
     'cartItems' => $CartServices->getCartItemsGrouped(),
     'csrf_token' => csrf_token(),

    
     ],
    );
  }

  public function store(Request $request,Product $Product,CartService $CartServices){

    $request->mergeIfMissing([
        'quantity' =>1
    ]);

    $data =$request->validate([
        'option_ids' => ['nullable','array'],
        'quantity' => ['nullable','integer','min:1'],
    ]);


    $CartServices->addItemToCart($Product,$data['quantity'],$data['option_ids']);

    return back()->with('success','product add to cart sucessfully!');
  }

  public function update(Request $request,Product $Product,CartService $CartServices){
    $request->validate([
        'quantity' => ['integer','min:1'],
    ]);
   $optionIds = $request->input('option_ids');
    $quantity = $request->input('quantity');

    $CartServices->updateItemQuantity($Product->id,$quantity,   $optionIds);

    return back()->with('success' ,'Quantity was updated');
  }
  public function destroy(Request $request,Product $Product,CartService $CartServices){

    $optionIds= $request->input('option_ids');

    $CartServices->removeItemFromCart($Product->id, $optionIds);

    return back()->with('success', 'product was removed from cart.');
  }

  public function checkout(Request $request, CartService $CartServices)
{
    Stripe::setApiKey(config('app.stripe_secret'));

    $vendorId = $request->input('vendor_id');

    $allCartItems = $CartServices->getCartItemsGrouped();

    DB::beginTransaction();
    try {
        $checkoutCartItems = $allCartItems;

        if ($vendorId && isset($allCartItems[$vendorId]) && is_array($allCartItems[$vendorId])) {
            $checkoutCartItems = [$allCartItems[$vendorId]];
        }

        $orders = [];
        $lineItems = [];

        foreach ($checkoutCartItems as $item) {
            $user = $item['user'];
            $cartItems = $item['items'];

            $order = Order::create([
                'stripe_session_id' => null,
                'user_id' => $request->user()->id,
                'vendor_user_id' => $user['id'],
                'total_price' => $item['total_price'],
                'status' => OrderStatusEnum::Draft->value,
                
            ]);

            $orders[] = $order;

            //  dd($cartItems);
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem['product_id'],
                    'price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'variation_type_option_ids' => $cartItem['option_ids'],
                ]);
               
                $description = collect($cartItem['option'])->map(function($item) {
                    return "{$item['type']['name']} : {$item['name']}";
                })->implode(', ');
                // dd($description)

                $lineItem = [
                    'price_data' => [
                        'currency' => config('app.currency'),
                        'product_data' => [
                            'name' => $cartItem['title'],
                            'images' => [$cartItem['image']],
                            'description' => !empty($description) ? $description : null,

                        ],
                        'unit_amount' => $cartItem['price'] * 100,
                    ],
                    'quantity' => $cartItem['quantity'],
                ];

                $lineItems[] = $lineItem;
            }

            $session = Session::create([
                'customer_email' => $request->user()->email,
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success') . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('stripe.failure'),
            ]);

            foreach ($orders as $order) {
                $order->stripe_session_id = $session->id;
                $order->save();
            }

            // Clear the cart after checkout session created
            $CartServices->clearCart($request->user()->id);

            DB::commit();

            return redirect($session->url);
        }
    } catch (\Throwable $th) {
      dd($th);
        DB::rollBack();
        Log::error($th);
        return back()->with('error', 'Something went wrong');
    }
}

}
