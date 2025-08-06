<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Signup API
     */
    public function signup(Request $request)
    {
        // 🔹 Step 1: Validate Request
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phone'     => ['required', 'unique:users,phone'],
            'password'  => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 🔹 Step 2: Generate OTP
        $otp = rand(100000, 999999);

        // 🔹 Step 3: Create User
        $user = User::create([
            'full_name'   => $request->full_name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'password'    => Hash::make($request->password),
            'otp'         => $otp,
            'is_verified' => 0,
        ]);

        // 🔹 Step 4: Return Response
        return response()->json([
            'status'  => true,
            'message' => 'Signup successful. OTP has been sent to your phone/email.',
            'data'    => [
                'id'        => $user->id,
                'full_name' => $user->full_name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'otp'       => $otp, // ❗ Show only during testing. Remove in production.
            ],
        ], 201);
    }
}
