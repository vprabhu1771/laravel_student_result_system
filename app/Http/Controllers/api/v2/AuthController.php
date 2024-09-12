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

    public function register(Request $request)
    {
        try{
            $request->validate([
                'name'=> 'required|string|max:255',
                'email'=> 'required|email|unique:users,email',
                'password'=> 'required',
                'device_name'=> 'required|string',
            ]);
            $user = User::create([
                'name'=>$request->input('name'),
                'email'=>$request->input('email'),
                'password'=>Hash::make($request->input('password')),
            ]);

            $recipient = $user->email; // Replace with actual recipient's email
            $subject = 'Custom Subject';
            $body = 'This is the body of the email. You can include HTML here if needed.';

            Mail::raw($body, function(Message $message) use ($recipient, $subject) {
                $message->to($recipient);
                $message->subject($subject);
                // You can add attachments or other options here if needed
            });
            
            return response()->json(['message'=>'User registered successfully','user'=>$user], 201);
        }catch(ValidationEception $e){
            return response()->json(['error' => $e->validate->errors()], 200);
        }
    }
    
    public function getUser(Request $request)
    {
        $user = $request->user();
        $user->photo_path =url('/storage/' .$user->photo_path);
        return $user;
    }
}
