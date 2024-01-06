<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        //
        $admins = Admin::take(20)->get();
        return $admins;
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
                'password' => 'required|string|min:6|max:255',

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }
        // Validate the incoming request data

        // Create a new resource instance
        $customer = Admin::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => $validatedData['password'],
        ]);

        // Return a JSON response indicating success
        return response()->json(['message' => 'Admin created successfully', 'data' => $customer], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return $admin;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the resource by its ID
        $admin = Admin::find($id);

        // Check if the brand exists
        if (!$admin) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        try {
            // Validate the incoming request data
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:15',
                'password' => 'required|string|min:6|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }

        // Update the customer with the validated data
        $admin->update($validatedData);
        return response()->json(['message' => 'Resource updated successfully', 'data' => $admin]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::find($id);
        // Check if the brand exists
        if (!$admin) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        // Delete the brand
        $admin->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|max:255',

        ]);

        $admin = Admin::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),

        ]);

        // Optionally, you can generate an access token for the registered customer
        $token = $admin->createToken('authToken')->accessToken;

        return response()->json(['customer' => $admin, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->guard('admin-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        return response()->json(auth()->guard('admin-api')->user());
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
}
