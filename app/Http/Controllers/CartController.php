<?php

namespace App\Http\Controllers;

use App\CartService;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
  public function index(CartService $CartServices){
     return Inertia::render('Cart/Index',[
     'cartItems' => $CartServices->getCartItemsGrouped(),
    
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

    return back()->with('sucess','product add to cart sucessfully!');
  }

  public function update(Request $request,Product $Product,CartService $CartServices){
    $request->validate([
        'quantity' => ['integer','min:1'],
    ]);
   $optionIds = $request->input('option_ids');
    $quantity = $request->input('quantity');

    $CartServices->updateItemQuantity($Product->id,$quantity,   $optionIds);

    return back()->with('sucess' ,'Quantity was updated');
  }
  public function destroy(Request $request,Product $Product,CartService $CartServices){

    $optionIds= $request->input('option_ids');

    $CartServices->removeItemFromCart($Product->id, $optionIds);

    return back()->with('sucess', 'product was removed from cart.');
  }

  public function checkout(){
    
  }
}
