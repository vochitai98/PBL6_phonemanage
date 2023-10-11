<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use Laravel\Prompts\Prompt;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions= Promotion::all();
        return $promotions;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'shop_id' => 'required|exists:shops,id',
            'product_id' => 'required|exists:products,id',
            'promotionPercentage' => 'nullable|integer|max:100',
            'promotionReduction' => 'nullable|integer|max:100',
            'detail' => 'required|string|max:255',
            'status' => 'required|boolean',
            'quantity' => 'required|integer',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
        ]);

        // Create a new resource instance
        $promotion = Promotion::create([
            'name' => $validatedData['name'],
            'shop_id' => $validatedData['shop_id'],
            'product_id' => $validatedData['product_id'],
            'promotionPercentage' => $validatedData['promotionPercentage'],
            'promotionReduction' => $validatedData['promotionReduction'],
            'detail' => $validatedData['detail'],
            'status' => $validatedData['status'],
            'quantity' => $validatedData['quantity'],
            'startDate' => $validatedData['startDate'],
            'endDate' => $validatedData['endDate'],
        ]);

        // Return a JSON response indicating success
        return response()->json(['message' => 'Promotion created successfully', 'data' => $promotion], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $promotion;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $promotion = Promotion::find($id);

        // Check if the review exists
        if (!$promotion) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'promotionPercentage' => 'nullable|integer|min:1|max:100',
            'promotionReduction' => 'nullable|integer|min:1|max:100',
            'detail' => 'required|string|max:255',
            'status' => 'required|boolean',
            'quantity' => 'required|integer',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
        ]);
        unset($validatedData['shop_id']);
        // Update the promotion with the validated data
        $promotion->update($validatedData);

        return response()->json(['message' => 'Resource updated successfully', 'data' => $promotion]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promotion = Promotion::find($id);
        // Check if the promotion exists
        if (!$promotion) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the promotion
        $promotion->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
}
