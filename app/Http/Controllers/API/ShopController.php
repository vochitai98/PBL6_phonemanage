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
        if (auth()->guard('customer-api')->check()) {
            $user = auth()->guard('customer-api')->user(); //get user currentlty
            $customer_id = $user->id; // get id user
            try {
                $validatedData = $request->validate([
                    'shopName' => 'required|string|max:255',
                    'shopAddress' => 'nullable|string|max:255',
                    'shopPhone' => 'nullable|string|max:255',
                    'state' => 'nullable|boolean|',
                    'bankAccount' => 'nullable|string|max:30',
                    //'customer_id' => 'required|exists:customers,id',
                    'vnp_TmnCode' => 'nullable|string|max:30',
                    'vnp_HashSecret' => 'nullable|string|max:60',

                    // Check if it exists in the "customers" table
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Handle validation errors
                return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
            }
            // Create a new record in the "shop" table
            $shop = Shop::create([
                'shopName' => $validatedData['shopName'],
                'shopAddress' => $validatedData['shopAddress'],
                'shopPhone' => $validatedData['shopPhone'],
                'state' => $validatedData['state'],
                'bankAccount' => $validatedData['bankAccount'],
                'customer_id' => $customer_id,
                'vnp_TmnCode' => $validatedData['vnp_TmnCode'],
                'vnp_HashSecret' => $validatedData['vnp_HashSecret'],
                // Set other fields accordingly
            ]);
            return response()->json(['message' => 'Shop has been created successfully', 'data' => $shop], 201);
        } else {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }
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
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'shopName' => 'required|string|max:255',
                'shopAddress' => 'nullable|string|max:255',
                'shopPhone' => 'nullable|string|max:255',
                'state' => 'nullable|boolean',
                'bankAccount' => 'nullable|string|max:30',
                'vnp_TmnCode' => 'nullable|string|max:30',
                'vnp_HashSecret' => 'nullable|string|max:60',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        // Validate the request data

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
    //Get shop by Customer_id
    public function getShopByCustomerId(Request $request)
    {
        if (!auth()->check() == false) {
            return response()->json(['message' => 'You are not loged in!'], 404);
        }
        $customer_id = auth()->id();
        $shop = Shop::where('customer_id', $request->input('id'))->first();

        if ($shop) {
            return response()->json(['data' => $shop], 200);
        } else {
            return response()->json(['message' => 'You have not created a store yet'], 404);
        }
    }
}
