<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product_Order;

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
        $validatedData= $request->validate([
            'shop_product_id' => 'required|exists:shops,id',// Check if it exists in the "shop_product" table
            'order_id' => 'required|exists:orders,id',// Check if it exists in the "order" table
            'quantity' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0'

        ]);

        $product_order = Product_Order::create([
           'shop_product_id' => $validatedData['shop_product_id'],
           'order_id' => $validatedData['order_id'],
           'quantity' => $validatedData['quantity'],
           'total' => $validatedData['total'],
        ]);
           
        return response()->json(['message' => 'resource has been created successfully','data' => $product_order], 201);
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

        // Validate the request data
        $validatedData= $request->validate([
            // 'shop_product_id' => 'required|exists:shops,id',// Check if it exists in the "shop_product" table
            // 'order_id' => 'required|exists:orders,id',// Check if it exists in the "order" table
            'quantity' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0'

        ]);
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
}
