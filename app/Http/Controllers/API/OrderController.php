<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::take(20)->get();
        return $orders;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'order_at' => 'required|date',
                'status' => 'required|boolean',
                'delivered' => 'required|boolean',
                'discount' => 'nullable|Integer|min:0',
                'deleted_at' => 'nullable|date',

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }



        // Create a new record in the "order" table
        $shop_product = Order::create([
            'customer_id' => $validatedData['customer_id'],
            'order_at' => $validatedData['order_at'],
            'status' => $validatedData['status'],
            'delivered' => $validatedData['delivered'],
            'discount' => $validatedData['discount'],
            'deleted_at' => $validatedData['deleted_at'],
            // Set other fields accordingly
        ]);
        return response()->json(['message' => 'resource has been created successfully', 'data' => $shop_product], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $order;
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::find($id);

        // Check if the brand exists
        if (!$order) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'order_at' => 'required|date',
                'status' => 'required|boolean',
                'delivered' => 'required|boolean',
                'discount' => 'nullable|Integer|min:0',
                'deleted_at' => 'nullable|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        // U

        unset($validatedData['customer_id']);

        $order->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $order]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::find($id);
        // Check if the order exists
        if (!$order) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the order
        $order->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
}
