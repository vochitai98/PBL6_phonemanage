<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        //
        $customers = Customer::all();
        return $customers;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:15|unique:customers,phone',
            'password' => 'required|string|min:6|max:255',
            'address' => 'nullable|string|max:255',
            'sex' => 'nullable|boolean',
            'accumulatedPoint' => 'nullable|integer',
            'dayOfBirth' => 'nullable|date',
            // Add validation rules for other fields
        ]);
        // Create a new resource instance
        $customer = Customer::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => $validatedData['password'],
            'address' => $validatedData['address'],
            'sex' => $validatedData['sex'],
            'accumulatedPoint' => $validatedData['accumulatedPoint'],
            'dayOfBirth' => $validatedData['dayOfBirth'],
            // Set other fields accordingly
        ]);

        // Return a JSON response indicating success
        return response()->json(['message' => 'Customer created successfully', 'data' => $customer], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $customer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the resource by its ID
        $customer = Customer::find($id);

        // Check if the brand exists
        if (!$customer) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6|max:255',
            'address' => 'nullable|string|max:255',
            'sex' => 'nullable|boolean',
            'accumulatedPoint' => 'nullable|integer',
            'dayOfBirth' => 'nullable|date',
        ]);
        // Update the customer with the validated data
        $customer->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $customer]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);
        // Check if the brand exists
        if (!$customer) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the brand
        $customer->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
    public function search(Request $request)
    {
        // Lấy thông tin tìm kiếm từ yêu cầu
        $data = $request->input('search');

        // Thực hiện tìm kiếm trong cơ sở dữ liệu
        $customers = Customer::where('name', 'like', '%' . $data . '%')
            ->orWhere('email', 'like', '%' . $data . '%')
            ->orWhere('phone', 'like', '%' . $data . '%')
            ->get();
        return response()->json($customers);
    }
}
