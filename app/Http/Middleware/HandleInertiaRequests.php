<?php

namespace App\Http\Middleware;

use App\CartService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request,): array
    {
        $cartServices = app(CartService::class);
        $totalQuantity = $cartServices->getTotalQuantity();
        $totalPrice = $cartServices->getTotalPrice();
        $cartItems = $cartServices->getCartItems();
        // dd(      $cartItems);
         return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'success'=> session('success'),
            'CartItems' => $cartItems,
            'totalPrice' => $totalPrice,
            'totalQuantity' =>$totalQuantity,
        ];
    }
}
