<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required',
                'player_id' => 'required'
            ]);

            // Retrieve the user by email
            $user = User::where('email', $request->email)->first();

            // Check if user exists and password matches
            if (! $user || ! Hash::check($request->password, $user->password)) {
                // Throw validation exception for incorrect credentials
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Save the player_id from the request
            $user->player_id = $request->player_id;
            $user->save();

            // Generate a token for the user
            $token = $user->createToken($request->device_name)->plainTextToken;

            // Return a success response with the generated token
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token
            ]);
        } 
        catch (ValidationException $e) {
            // Return validation error response
            return response()->json(['error' => $e->validator->errors()], 422);
        } 
        catch (Exception $e) {
            // Handle unexpected exceptions
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->player_id = null; // Remove player_id on logout
        $user->save();
        
        Auth::logout();
        return response()->json(['success' => true, 'message' => 'Logout successful']);
    }
}
