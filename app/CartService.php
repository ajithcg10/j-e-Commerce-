<?php

namespace App;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\VariationType;
use App\Models\VariationTypeOption;
use Auth;
use Cookie;
use DB;
use Log;

use function PHPSTORM_META\map;

class  CartService
{
    private ?array $cachedCartItems =null;

    protected const COOKIE_NAME ='cartItems';

    protected const COOKIE_LIFETIME =60* 24* 365;


    public function addItemToCart(Product $product,int $quantity =1, $optionIds=null){

        if($optionIds === null){
            $optionIds = $product->variationTypes->mapWithKeys(fn(VariationType $type)=> [$type->id => $type->options[0]?->id])
            ->toArray();
        }
        $price = $product->getPriceForOptions($optionIds);
       
        if(Auth::check()){
            $this->saveItemToDatabase($product->id,$quantity, $price ,$optionIds);
        }
        else{
               $this->saveItemToCookies($product->id,$quantity,$price,$optionIds);
        }

    }
    public function updateItemQuantity(int $productId ,int $quantity, $optionIds =null){
        if(Auth::check()){
            $this->updateItemQuantityInDatabase($productId,$quantity,$optionIds);
            
        }
        else{
        //  dd($optionIds);
            $this->updateItemQuantityInCookies($productId, $quantity, json_decode($optionIds, true));

        }

    }
    public function  removeItemFromCart(int $productId,$optionIds =null){
         if(Auth::check()){
            $this->reomveItemFromInDatabase($productId,$optionIds);
        }
        else{
              
            $this->reomveItemFromInCookies($productId, json_decode($optionIds,true));
        }

    }
    public function getCartItems():array
    {
        try {
            if($this->cachedCartItems === null){
                if(Auth::check()){
                    $cartItems = $this->getCartItemsFromDatabase();
                    // dd("aa");
                }
                else{
                    $cartItems = $this->getCartItemsFromCookies();
                    //   dd("aa");

                }  

               $productIds = collect($cartItems)->map(fn($item) => $item['product_id']);
        
              
                $products = Product::whereIn('id',$productIds)->with('user.vendor')->forWebsite()->get()->keyBy('id');
                $cartItemData =[];
                foreach ($cartItems as $key => $cartItem){
              
                    $product =data_get($products,$cartItem['product_id']);
                    if (!$product) continue;
                    $optionInfo=[];
                   $optionIds = json_decode($cartItem['option_ids'], true);
                   $options = VariationTypeOption::with('variationType')->whereIn('id', array_values($optionIds))->get()->keyBy('id');
                //    dd(  $options);
                    $imageUrl =null;

                   foreach($optionIds as $optionId){
            
                            $option = data_get($options, $optionId);
                            if (!$option) continue;
  
                            if(!$imageUrl){
                                $imageUrl = $option->getFirstMediaUrl('image','small');
                            }
                   

                            $optionInfo[] = [
                                'id' => $optionId,
                                'name' => $option->name,
                                'type' => [
                                    'id' => $option->variationType->id,
                                    'name' => $option->variationType->name,
                                ]
                            ];
                        }

                    $cartItemData []=[
                        'id' => $cartItem['id'],
                        'product_id' =>$product->id,
                        'title' =>$product->title,
                        'slug' =>$product->slug,
                        'price' =>$cartItem['price'],
                        'quantity' =>$cartItem['quantity'],
                        'option_ids' =>$cartItem['option_ids'],
                        'option' =>$optionInfo,
                        'image' =>$imageUrl ?: $product->getFirstMediaUrl('image','small'),
                        'user' => [
                            'id' =>$product->created_by,
                            'name' =>$product->user->vendor->store_name,
                        ]

                    ];
                }

                $this->cachedCartItems = $cartItemData;

            }
     
            return $this->cachedCartItems;
        
        } catch (\Exception $e) {
            // throw $e;
           Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
        return [];
    }
    public function getTotalQuantity():int
    {
        $totalQuantity =0;
        foreach ($this->getCartItems() as  $item) {
            $totalQuantity += $item['quantity'];
        }
        return  $totalQuantity;
    }
    public function getTotalPrice():float
    {
        $totalprice =0;
        foreach ($this->getCartItems() as  $item) {
            $totalprice += $item['quantity'] * $item['price'];
        }
        return  $totalprice;

    }

    protected function updateItemQuantityInDatabase(int $productId,int $quantity,array $optionIds):void
    {
        $userId = Auth::id();

        $cartItem = CartItem::where('user_id',$userId)
        ->where('product_id',$productId)
        ->where('variation_type_option_ids',json_encode($optionIds))
        ->first();
        if($cartItem){
            $cartItem->update([
                'quantity'=>$quantity,
            ]);
        }

    }
    protected function updateItemQuantityInCookies(int $productId, int $quantity, array $optionIds): void
{
    $cartItems = $this->getCartItemsFromCookies();

    ksort($optionIds); // ensure consistent order
    $optionIdsJson = json_encode($optionIds);

    foreach ($cartItems as &$item) {
        if (
            $item['product_id'] === $productId &&
            $item['option_ids'] === $optionIdsJson
        ) {
            $item['quantity'] = $quantity; // Set, don't add
            break;
        }
    }

    // Save updated cart items
    Cookie::queue(self::COOKIE_NAME, json_encode(array_values($cartItems)), self::COOKIE_LIFETIME);
}

    protected function saveItemToDatabase(int $productId, int $quantity, $price, array $optionIds): void
{
    $userId = Auth::id();


    // Ensure consistent key order in the options array
    ksort($optionIds);

    // Attempt to find an existing cart item with the same product and options
    $cartItem = CartItem::where('user_id', $userId)
        ->where('product_id', $productId)
        ->where('variation_types_option_ids', '!=', null)
        ->whereRaw("variation_types_option_ids::jsonb = ?", [json_encode($optionIds)])
        ->first();

    if ($cartItem) {
        // Update the quantity
        $cartItem->update([
            'quantity' => DB::raw('quantity + ' . $quantity),
        ]);
    } else {
        // Create a new cart item
      $cartItem = CartItem::create([
        'user_id' => $userId,
        'product_id' => $productId,
        'quantity' => $quantity,
        'price' => $price,
        'variation_types_option_ids' => json_encode($optionIds),
]);

    }

    // Store the cart item in a cookie
    Cookie::queue(self::COOKIE_NAME, json_encode($cartItem), self::COOKIE_LIFETIME);
}

 protected function saveItemToCookies(int $productId, int $quantity, $price, array $optionIds): void
{
    $cartItems = $this->getCartItemsFromCookies();
    ksort($optionIds); // to ensure consistent JSON

    $optionIdsJson = json_encode($optionIds);
    $itemFound = false;

    foreach ($cartItems as &$item) {
        if ($item['product_id'] === $productId && $item['option_ids'] === $optionIdsJson) {
            $item['quantity'] += $quantity;
            $itemFound = true;
            break;
        }
    }

    if (!$itemFound) {
        $cartItems[] = [
            'id' => (string) \Str::uuid(),
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'option_ids' => $optionIdsJson,
        ];
    }

    // Save back as a clean indexed array
    Cookie::queue(self::COOKIE_NAME, json_encode(array_values($cartItems)), self::COOKIE_LIFETIME);
}



    protected function reomveItemFromInDatabase(int $productId,array $optionIds):void
    {
        $userId = Auth::id();
        ksort($optionIds);
        CartItem::where('user_id',$userId)
        ->where('product_id',$productId)->where('variation_type_option_ids',json_encode($optionIds))
        ->delete();

    }
   protected function reomveItemFromInCookies(int $productId, array $optionIds): void
{
    $cartItems = $this->getCartItemsFromCookies();
    ksort($optionIds);
    $optionIdsJson = json_encode($optionIds);

    foreach ($cartItems as $index => $item) {
        if (
            $item['product_id'] === $productId &&
            $item['option_ids'] === $optionIdsJson
        ) {
            unset($cartItems[$index]);
            break; // stop after removing the matching item
        }
    }

    // Reindex the array before saving
    Cookie::queue(self::COOKIE_NAME, json_encode(array_values($cartItems)), self::COOKIE_LIFETIME);
}

    protected function getCartItemsFromDatabase()
    {
        $userId = Auth::id();
    
        $cartItems = CartItem::where('user_id',$userId)
        ->get()
        ->map(function($cartItem){
 
            return [
                'id' => $cartItem->id,
                'product_id' =>$cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'option_ids' =>$cartItem->variation_types_option_ids,
            ];
        })->toArray();

        return  $cartItems;
    }
 protected function getCartItemsFromCookies(): array
{
    $cartItems = json_decode(Cookie::get(self::COOKIE_NAME, '[]'), true);

     

    // Ensure it's a proper array
    if (!is_array($cartItems)) {
        return [];
    }

    return array_values($cartItems); // clean reindex
}

public function getCartItemsGrouped(): array
{
    $cartItems = $this->getCartItems();

    return collect($cartItems)->groupBy(fn($item)=>$item['user']['id'])->map(fn($items,$userId)=>[
        'user' => $items->first()['user'],
        'items' => $items->toArray(),
        'total_quantity' => $items->sum('quantity'),
        'total_price' => $items->sum(fn($item)=>$item['quantity'] * $item['price']),
    ])->toArray();
}

}
