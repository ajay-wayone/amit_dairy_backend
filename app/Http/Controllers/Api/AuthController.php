<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // 📝 Signup Method
    public function signup(Request $request)
    {
        // 🔸 Step 1: Validate Input
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'required|digits:10|unique:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 🔸 Step 2: Generate OTP
        $otp = rand(100000, 999999);

        // 🔸 Step 3: Create User
        $user = User::create([
            'full_name'   => $request->full_name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'otp'         => $otp,
            'is_verified' => 0,
        ]);

        // 🔸 Step 4: Send Response
        return response()->json([
            'status'  => true,
            'message' => 'Signup successful. OTP sent.',
            'data'    => $user,
        ]);
    }
}
