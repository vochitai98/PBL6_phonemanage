<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;

use function Psy\sh;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shops = Shop::all();
        return $shops;
    }

    /**
     * Show the form for creating a new resource.
     */

     public function store(Request $request)
     {
         // Validate the input data (you can add more validation rules if needed)
         $validatedData= $request->validate([
             'shopName' => 'required|string|max:255',
             'shopAddress' => 'nullable|string|max:255',
             'shopPhone' => 'nullable|string|max:255',
             'state' => 'nullable|boolean|',
             'bankAccount' => 'nullable|string|max:30',
             'customer_id' => 'required|exists:customers,id',
             
              // Check if it exists in the "customers" table
         ]);
     
         // Create a new record in the "shop" table
         $shop = Shop::create([
            'shopName' => $validatedData['shopName'],
            'shopAddress' => $validatedData['shopAddress'],
            'shopPhone' => $validatedData['shopPhone'],
            'state' => $validatedData['state'],
            'bankAccount' => $validatedData['bankAccount'],
            'customer_id' => $validatedData['customer_id'], 
            // Set other fields accordingly
         ]);
         return response()->json(['message' => 'Shop has been created successfully','data' => $shop], 201);
     }
     

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $shop;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = Shop::find($id);

        // Check if the shop exists
        if (!$shop) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        // Validate the request data
        $validatedData= $request->validate([
            'shopName' => 'required|string|max:255',
            'shopAddress' => 'nullable|string|max:255',
            'shopPhone' => 'nullable|string|max:255',
            'state' => 'nullable|boolean',
            'bankAccount' => 'nullable|string|max:30',
        ]);
        unset($validatedData['customer_id']);
        // Update the customer with the validated data
        $shop->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $shop]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop = Shop::find($id);
        // Check if the brand exists
        if (!$shop) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the brand
        $shop->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }

    public function search(Request $request)
    {
        // Lấy thông tin tìm kiếm từ yêu cầu
        $data = $request->input('search');

        $shops = Shop::where('shopName', 'like', '%' . $data . '%')
            ->orWhere('shopAddress', 'like', '%' . $data . '%')
            ->orWhere('shopPhone', 'like', '%' . $data . '%')
            ->get();
        return response()->json($shops);
    }
}
