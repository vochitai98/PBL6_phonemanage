<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

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
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }


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
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

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

    public function register(Request $request)
    {
        // Validate the incoming request data
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:15|unique:customers,phone',
                'password' => 'required|string|min:6|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        $customer = Customer::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => Hash::make($validatedData['password']),
        ]);
        // Optionally, you can generate an access token for the registered customer
        $token = $customer->createToken('authToken')->accessToken;
        return response()->json(['customer' => $customer, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->guard('customer-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    public function me()
    {
        return response()->json(auth()->guard('customer-api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
}
