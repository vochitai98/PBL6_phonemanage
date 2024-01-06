<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Review;
use App\Models\Shop_Product;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::take(20)->get();
        return $reviews;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user();
            $customer_id = $user->id;
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                //'customer_id' => 'required|exists:customers,id',
                //'shop_product_id' => 'required|exists:shop_product_id,id',
                'feedback' => 'string|max:255',
                'rating' => 'required|integer|min:1|max:5',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }
        $review = DB::table('orders')
            ->where('customer_id', '=', $customer_id)
            ->where('shop', '=', $customer_id)
            ->get()
            ->first();
        $shop_product_id = $request->input('shop_product_id');
        // Create a new resource instance
        $review = Review::create([
            'customer_id' => $customer_id,
            'shop_product_id' => $shop_product_id,
            'feedback' => $validatedData['feedback'],
            'rating' => $validatedData['rating'],
        ]);

        // Return a JSON response indicating success
        return response()->json(['message' => 'Brands created successfully', 'data' => $review], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $review;
    }
    public function getReviewByCustomer(Request $request)
    {
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user();
            $customer_id = $user->id;
            $shop_product_id = $request->input('shop_product_id');
            $review = DB::table('reviews')
            ->where('customer_id', '=', $customer_id)
            ->where('shop_product_id', '=', $shop_product_id)
            ->first();

        return $review;
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user();
            $customer_id = $user->id;
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
        $review = Review::find($id);
        // Check if the review exists
        if (!$review) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        $cus_id = $review->customer_id;
        if ($customer_id != $cus_id) {
            return response()->json(['message' => 'Unauthorized. You are not owner!'], 401);
        }
        try {
            $validatedData = $request->validate([
                //'product_id' => 'required|exists:products,id',
                'feedback' => 'required|string|max:255',
                'rating' => 'required|integer|min:1|max:5',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }
        unset($validatedData['product_id']);
        unset($validatedData['customer_id']);
        // Update the brand with the validated data
        $review->update($validatedData);

        return response()->json(['message' => 'Resource updated successfully', 'data' => $review]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user();
            $customer_id = $user->id;
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
        $review = Review::find($id);
        // Check if the review exists
        if (!$review) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        $cus_id = $review->customer_id;
        if ($customer_id != $cus_id) {
            return response()->json(['message' => 'Unauthorized. You are not owner!'], 401);
        }
        // Delete the brand
        $review->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
    public function review(Request $request)
    {
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user();
            $customer_id = $user->id;
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                //'customer_id' => 'required|exists:customers,id',
                //'shop_product_id' => 'required|exists:shop_product_id,id',
                'feedback' => 'string|max:255',
                'rating' => 'required|integer|min:1|max:5',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        $order_id = $request->input('order_id');
        $status_order = Order::find($order_id)->status;
        if ($status_order !== "completed") {
            return response()->json(['message' => 'you are not Purchased!'], 422);
        }
        $shop_product_id = DB::table('product_orders')
            ->select('shop_product_id')
            ->where('order_id', '=', $order_id)
            ->first()->shop_product_id;

        $review = Review::create([
            'customer_id' => $customer_id,
            'shop_product_id' => $shop_product_id,
            'feedback' => $validatedData['feedback'],
            'rating' => $validatedData['rating'],
        ]);
        if ($review) {
            $count_review = DB::table('reviews')
                ->where('shop_product_id', '=', $shop_product_id)
                ->count();
            $shop_product = Shop_Product::find($shop_product_id);
            $shop_product->starRated = ($shop_product->starRated * ($count_review - 1) + $review->rating) / $count_review;
            $shop_product->save();
        }
        return response()->json($review);
    }
}
