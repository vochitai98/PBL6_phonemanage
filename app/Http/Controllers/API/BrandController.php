<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $brands = Brand::all();
        return $brands;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            // Add validation rules for other fields
        ]);

        // Create a new resource instance
        $brands = Brand::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            // Set other fields accordingly
        ]);

        // Return a JSON response indicating success
        return response()->json(['message' => 'Brands created successfully', 'data' => $brands], 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $brand = Brand::find($id);
        return $brand;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the resource by its ID
        $brand = Brand::find($id);

        // Check if the brand exists
        if (!$brand) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            // Add validation rules for other fields
        ]);

        // Update the brand with the validated data
        $brand->update($validatedData);

        return response()->json(['message' => 'Resource updated successfully', 'data' => $brand]);
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::find($id);
        // Check if the brand exists
        if (!$brand) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the brand
        $brand->delete();
        return response()->json(['message' => 'Resource deleted successfully']);

    }
}
