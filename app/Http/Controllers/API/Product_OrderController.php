<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product_Order;
use App\Models\Order;
use App\Models\Shop_Product;
use Illuminate\Support\Facades\DB;

class Product_OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product_orders = Product_Order::all();
        return $product_orders;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'shop_product_id' => 'required|exists:shops,id', // Check if it exists in the "shop_product" table
                'order_id' => 'required|exists:orders,id', // Check if it exists in the "order" table
                'quantity' => 'required|integer|min:1',
                'total' => 'required|numeric|min:0'

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }



        $product_order = Product_Order::create([
            'shop_product_id' => $validatedData['shop_product_id'],
            'order_id' => $validatedData['order_id'],
            'quantity' => $validatedData['quantity'],
            'total' => $validatedData['total'],
        ]);

        return response()->json(['message' => 'resource has been created successfully', 'data' => $product_order], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product_order = Product_Order::find($id);
        if (!$product_order) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $product_order;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product_order = Product_Order::find($id);

        // Check if the brand exists
        if (!$product_order) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        try {
            // Validate the request data
            $validatedData = $request->validate([
                // 'shop_product_id' => 'required|exists:shops,id',// Check if it exists in the "shop_product" table
                // 'order_id' => 'required|exists:orders,id',// Check if it exists in the "order" table
                'quantity' => 'required|integer|min:1',

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        unset($validatedData['shop_product_id']);
        unset($validatedData['order_id']);
        $product_order->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $product_order]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product_order = Product_Order::find($id);
        // Check if the order exists
        if (!$product_order) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the order
        $product_order->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
    //Add to cart
    public function addToCart(Request $request)
    {
        // Validate request data
        try {
            $validatedData = $request->validate([
                'shop_product_id' => 'required|exists:shop_products,id',
                'quantity' => 'required|integer|min:1',
                'order_id' => 'nullable'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }
        
        $quantity_product= Shop_Product::find($validatedData['shop_product_id'])->quantity;
        // Assuming you have an authenticated user
        $user_id = auth()->guard('customer-api')->id();

        // Find the user's open order or create a new one
        $order = Order::firstOrCreate([
            'customer_id' => $user_id,
            'status' => 'cart', // or any other status indicating a cart
        ]);

        // Check if the product is already in the cart, update quantity if true
        $existingShopProduct = DB::table('product_orders')->where('shop_product_id',$request->input('shop_product_id'))->first();
        if ($existingShopProduct) {
            //dd($existingShopProduct);
            $record = Product_Order::find($existingShopProduct->id);
            $quantity = $record->quantity + $request->input('quantity');
            if($quantity > $quantity_product){
                return response()->json(['message' => 'The quantity of items in stock is not enough!']);
            }
            $record->update(['quantity' => $quantity]);
            $product_order = $record;
        } else {
            if( $validatedData['quantity'] > $quantity_product){
                return response()->json(['message' => 'The quantity of items in stock is not enough!']);
            }
            $product_order = Product_Order::create([
                'shop_product_id' => $validatedData['shop_product_id'],
                'order_id' => $order->id,
                'quantity' => $validatedData['quantity'],
            ]);
            // Otherwise, attach the product to the cart with quantity
        }

        return response()->json(['message' => 'Product added to cart successfully','data'=>$product_order]);
    }

    //view cart
    public function viewCart(){
        if(!auth()->guard('customer-api')->check()){
            return response()->json(['message' => 'You are not loged in!']);
        }
        $order = DB::table('orders')
            ->select('*')
            ->where('orders.customer_id',auth()->guard('customer-api')->id())
            ->Where('orders.status','cart')
            ->first();
        $product_orders = DB::table('product_orders')
            ->select('product_orders.id','products.name','shop_products.price','product_orders.quantity as quantity_order','shop_products.quantity as quantity_product')
            ->join('shop_products', 'product_orders.shop_product_id', '=', 'shop_products.id')
            ->join('products', 'products.id', '=', 'shop_products.product_id')
            ->where('product_orders.order_id',$order->id)
            ->get();
        dd($product_orders);
        return response()->json(['data'=>$product_orders]);
    
    }

    public function in_decreaseAmount(Request $request){
        if(!auth()->guard('customer-api')->check()){
            return response()->json(['message' => 'You are not loged in!']);
        }
        $id=$request->product_order_id;
        $product_order = Product_Order::find($id);
        if($request->has('increase')){
            $product_order->quantity +=1;
            if($request->input('quantity_product')< $product_order->quantity){
            return response()->json(['message'=>'Sorry, the product is temporarily out of stock!']);
            }
        }else{
            $product_order->quantity -=1;
            if($product_order->quantity == 0){
                $product_order->delete();
                return response()->json(['message'=>'Deleted sussecefull!']);
                }
        }
        $product_order->save();
        return response()->json(['data'=>$product_order]);
    
    }

}
