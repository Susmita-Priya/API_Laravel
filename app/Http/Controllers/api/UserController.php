<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function Registration(Request $request)
    {
        // Validate input
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);


        try {
            // Create user with verification code
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
            ], 201); // 201 Created
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again later.'
            ], 500); // 500 Internal Server Error
        }
    }


    public function login(Request $request)
    {
        // Validate input
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log in the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Generate a new token
            $token = $user->createToken('YourAppName')->plainTextToken;

            // Return a success response with the token
            return response()->json([
                'message' => 'Login successful.',
                'token' => $token,
                'user' => $user, // Optionally return user data
            ], 200);
        } else {
            // Return error response for invalid credentials 
            return response()->json([
                'error' => 'Invalid email or password.',
            ], 401); // 401 Unauthorized
        }
    }


    public function userInfo()
    {
        $user = User::all();

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user, // Optionally return user data
        ], 200);
    }
    
}
