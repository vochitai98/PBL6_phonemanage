<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::all();
        return $reviews;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'feedback' => 'string|max:255',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Create a new resource instance
        $review = Review::create([
            'customer_id' => $validatedData['customer_id'],
            'product_id' => $validatedData['product_id'],
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


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $review = Review::find($id);

        // Check if the review exists
        if (!$review) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'feedback' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
        ]);
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
        $review = Review::find($id);
        // Check if the review exists
        if (!$review) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the brand
        $review->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
}