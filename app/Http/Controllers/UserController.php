<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            // 'phone' => 'required|string|max:15|unique:customers,phone',
            'password' => 'required|string|min:6|max:255',
            // 'address' => 'nullable|string|max:255',
            // 'sex' => 'nullable|boolean',
            // 'accumulatedPoint' => 'nullable|integer',
            // 'dayOfBirth' => 'nullable|date',
            // Add validation rules for other fields
        ]);
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            //'phone' => $validatedData['phone'],
            'password' => Hash::make($validatedData['password']),
            // 'address' => $validatedData['address'],
            // 'sex' => $validatedData['sex'],
            // 'accumulatedPoint' => $validatedData['accumulatedPoint'],
            // 'dayOfBirth' => $validatedData['dayOfBirth'],
            // Set other fields accordingly
        ]);
        // Optionally, you can generate an access token for the registered customer
        $token = $user->createToken('authToken')->accessToken;
        return response()->json(['customer' => $user, 'token' => $token], 201);
    
    }
}

