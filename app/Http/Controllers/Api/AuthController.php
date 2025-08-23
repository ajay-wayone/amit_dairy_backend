<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Signup API
     */
    public function signup(Request $request)
    {
        try {
            // Step 1: Validation
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email'     => 'required|email|unique:users,email',
                'phone'     => 'required|unique:users,phone',
                'password'  => 'required|string|min:6|confirmed',
                'purpose'   => 'required|in:signup,login,resetpassword,password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation errors',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Step 2: Generate OTP
            $otp = rand(100000, 999999);

            // Step 3: Create User
            $user = User::create([
                'full_name'   => $request->full_name,
                'email'       => $request->email,
                'phone'       => $request->phone,
                'password'    => Hash::make($request->password),
                'purpose'     => $request->purpose,
                'otp'         => $otp,
                'is_verified' => 0,
            ]);

            // Step 4: Return response with OTP directly
            return response()->json([
                'status'  => true,
                'message' => 'Signup successful. Use OTP to verify your account.',
                'data'    => [
                    'id'        => $user->id,
                    'full_name' => $user->full_name,
                    'email'     => $user->email,
                    'phone'     => $user->phone,
                    'purpose'   => $user->purpose,
                    'otp'       => $otp, // OTP comes in api
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify OTP API
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'   => 'required|email|exists:users,email',
                'otp'     => 'required|digits:6',
                'purpose' => 'required|in:signup,login,resetpassword,forgetpassword',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('purpose', $request->purpose)
                ->first();

            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid OTP or purpose',
                ], 400);
            }

            // ✅ Clear OTP & Purpose
            $user->otp     = null;
            $user->purpose = null;

            // ✅ Handle according to purpose
            switch ($request->purpose) {
                case 'signup':
                    $user->is_verified       = 1;
                    $user->email_verified_at = now();
                    $user->save();
                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'status'  => true,
                        'message' => 'Signup verified successfully',
                        'data'    => [
                            'id'        => $user->id,
                            'full_name' => $user->full_name,
                            'email'     => $user->email,
                            'token'     => $token,
                        ],
                    ]);

                case 'login':
                    $user->save();
                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'status'  => true,
                        'message' => 'Login verified successfully',
                        'data'    => [
                            'id'        => $user->id,
                            'full_name' => $user->full_name,
                            'email'     => $user->email,
                            'token'     => $token,
                        ],
                    ]);

                case 'resetpassword':
                    $user->save();
                    // ⚠️ Generate temporary token for password reset
                    $resetToken = Str::random(60);

                    return response()->json([
                        'status'      => true,
                        'message'     => 'OTP verified, proceed to reset password',
                        'reset_token' => $resetToken,
                    ]);

                case 'forgetpassword':
                    $user->save();
                    return response()->json([
                        'status'  => true,
                        'message' => 'OTP verified, you can set a new password now',
                    ]);

                default:
                    return response()->json([
                        'status'  => false,
                        'message' => 'Invalid purpose',
                    ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong! otp',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login API
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // ✅ Generate OTP for login purpose
            $otp = rand(100000, 999999);
            $user->update([
                'otp'     => $otp,
                'purpose' => 'login',
            ]);

            // 📩 Yaha tum OTP email/SMS kar sakte ho
            // Mail::to($user->email)->send(new LoginOtpMail($otp));

            return response()->json([
                'status'  => true,
                'message' => 'OTP sent for login verification',
                'data'    => [
                    'email'   => $user->email,
                    'purpose' => $user->purpose,
                    'otp'     => $otp, 
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Forgot Password API
     */

    public function forgotPassword(Request $request)
    {
        try {
            // Step 1: Validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'phone' => 'required|exists:users,phone',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Step 2: Get user
            $user = User::where('email', $request->email)
                ->where('phone', $request->phone)
                ->first();

            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not found with provided email & phone.',
                ], 404);
            }

            // Step 3: Generate OTP
            $otp = rand(100000, 999999);

            // Step 4: Save OTP & purpose in users table
            $user->otp     = $otp;
            $user->purpose = 'forgetpassword';
            $user->save();

            // Step 5: Response with OTP
            return response()->json([
                'status'  => true,
                'message' => 'OTP generated successfully. It will expire in 10 minutes.',
                'otp'     => $otp, // Remove in production
                'purpose' => 'forgetpassword',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset Password API
     */
    public function resetPassword(Request $request)
    {
        try {
            // Step 1: Validate request
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email|exists:users,email',
                'otp'      => 'required|digits:6',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Step 2: Find user by email + OTP
            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid OTP',
                ], 400);
            }

            //OPTIONAL: Check OTP expiry if otp_created_at is stored
            if (now()->diffInMinutes($user->otp_created_at) > 5) {
                return response()->json([
                    'status'  => false,
                    'message' => 'OTP expired',
                ], 400);
            }

            // Step 3: Update password
            $user->password = Hash::make($request->password);
            $user->otp      = null;
            $user->save();

            return response()->json([
                'status'  => true,
                'message' => 'Password reset successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout API
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Logged out successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get User Profile API
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'status'  => true,
                'message' => 'Profile retrieved successfully',
                'data'    => [
                    'id'          => $user->id,
                    'full_name'   => $user->full_name,
                    'email'       => $user->email,
                    'phone'       => $user->phone,
                    'is_verified' => $user->is_verified,
                    'created_at'  => $user->created_at,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update Profile API
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'full_name' => 'sometimes|string|max:255',
                'phone'     => 'sometimes|unique:users,phone,' . $user->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user->update($request->only(['full_name', 'phone']));

            return response()->json([
                'status'  => true,
                'message' => 'Profile updated successfully',
                'data'    => [
                    'id'        => $user->id,
                    'full_name' => $user->full_name,
                    'email'     => $user->email,
                    'phone'     => $user->phone,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create Password API
     */
    public function createPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email|exists:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)
                ->where(function ($query) use ($request) {
                    $query->where('otp', $request->otp)
                        ->orWhere('purpose', $request->purpose);
                })
                ->first();

            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid OTP or email',
                ], 400);
            }

            // Update password and clear OTP
            $user->password = Hash::make($request->password);
            $user->otp      = null;
            $user->purpose  = null;
            $user->save();

            return response()->json([
                'status'  => true,
                'message' => 'Password created successfully',
                'data'    => [
                    'id'        => $user->id,
                    'full_name' => $user->full_name,
                    'email'     => $user->email,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
