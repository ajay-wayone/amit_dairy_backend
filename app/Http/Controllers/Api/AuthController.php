<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\DB;

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
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|unique:users,phone',
                'password' => 'required|string|min:6|confirmed',
                'purpose' => 'required|in:signup,login,resetpassword,password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            // Step 2: Generate OTP
            $otp = rand(100000, 999999);

            // Step 3: Create User
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'purpose' => $request->purpose,
                'otp' => $otp,
                'is_verified' => 0,
            ]);

            // Send notification for signup
            $user->notify(new UserNotification(
                'Account Created',
                'Your account has been created. Please verify OTP to activate.',
                ['user_id' => $user->id, 'type' => 'signup']
            ));

            // Step 4: Return response with OTP directly
            return response()->json([
                'status' => true,
                'message' => 'Signup successful. Use OTP to verify your account.',
                'data' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'purpose' => $user->purpose,
                    'otp' => $otp,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }






    public function getCurrentUser(Request $request)
    {
        try {
            $user = $request->user(); // token se user milega

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            return response()->json([
                'status' => true,
                'message' => 'Current user fetched successfully',
                'data' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'is_verified' => $user->is_verified,
                    'purpose' => $user->purpose,
                    'status' => $user->status,
                    'created_at' => $user->created_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
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
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|digits:6',
                'purpose' => 'required|in:signup,login,resetpassword,forgetpassword',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('purpose', $request->purpose)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP or purpose',
                ], 400);
            }

            // ✅ Clear OTP & Purpose
            $user->otp = null;
            $user->purpose = null;

            // ✅ Handle according to purpose
            switch ($request->purpose) {
                case 'signup':
                    $user->is_verified = 1;
                    $user->email_verified_at = now();
                    $user->save();

                    // Send notification for verified signup
                    $user->notify(new UserNotification(
                        'Account Verified',
                        'Your account is now verified and active.',
                        ['user_id' => $user->id, 'type' => 'signup_verified']
                    ));

                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'status' => true,
                        'message' => 'Signup verified successfully',
                        'data' => [
                            'id' => $user->id,
                            'full_name' => $user->full_name,
                            'email' => $user->email,
                            'token' => $token,
                        ],
                    ]);

                case 'login':
                    $user->save();

                    // Send notification for login
                    $user->notify(new UserNotification(
                        'Login Successful',
                        'You have successfully logged in to your account.',
                        ['user_id' => $user->id, 'type' => 'login']
                    ));

                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'status' => true,
                        'message' => 'Login verified successfully',
                        'data' => [
                            'id' => $user->id,
                            'full_name' => $user->full_name,
                            'email' => $user->email,
                            'token' => $token,
                        ],
                    ]);

                case 'resetpassword':
                    $user->save();

                    // Send notification for password reset request
                    $user->notify(new UserNotification(
                        'Password Reset Requested',
                        'You can now reset your password using the provided token.',
                        ['user_id' => $user->id, 'type' => 'password_reset']
                    ));

                    // ⚠️ Generate temporary token for password reset
                    $resetToken = Str::random(60);

                    return response()->json([
                        'status' => true,
                        'message' => 'OTP verified, proceed to reset password',
                        'reset_token' => $resetToken,
                    ]);

                case 'forgetpassword':
                    $user->save();

                    // Send notification for password reset OTP verification
                    $user->notify(new UserNotification(
                        'Password Reset OTP Verified',
                        'You can now set a new password for your account.',
                        ['user_id' => $user->id, 'type' => 'password_reset_verified']
                    ));

                    return response()->json([
                        'status' => true,
                        'message' => 'OTP verified, you can set a new password now',
                    ]);

                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid purpose',
                    ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong! otp',
                'error' => $e->getMessage(),
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
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // ✅ Generate OTP for login purpose
            $otp = rand(100000, 999999);
            $user->update([
                'otp' => $otp,
                'purpose' => 'login',
            ]);

            // Send notification for OTP generation
            $user->notify(new UserNotification(
                'Login OTP Generated',
                'Your login OTP has been generated. Please use it to complete your login.',
                ['user_id' => $user->id, 'type' => 'login_otp']
            ));

            // 📩 Yaha tum OTP email/SMS kar sakte ho
            // Mail::to($user->email)->send(new LoginOtpMail($otp));

            return response()->json([
                'status' => true,
                'message' => 'OTP sent for login verification',
                'data' => [
                    'email' => $user->email,
                    'purpose' => $user->purpose,
                    'otp' => $otp,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
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
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Step 2: Get user
            $user = User::where('email', $request->email)
                ->where('phone', $request->phone)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found with provided email & phone.',
                ], 404);
            }

            // Step 3: Generate OTP
            $otp = rand(100000, 999999);

            // Step 4: Save OTP & purpose in users table
            $user->otp = $otp;
            $user->purpose = 'forgetpassword';
            $user->save();

            // Send notification for password reset OTP
            $user->notify(new UserNotification(
                'Password Reset OTP',
                'Your password reset OTP has been generated. Please use it to reset your password.',
                ['user_id' => $user->id, 'type' => 'password_reset_otp']
            ));

            // Step 5: Response with OTP
            return response()->json([
                'status' => true,
                'message' => 'OTP generated successfully. It will expire in 10 minutes.',
                'otp' => $otp, // Remove in production
                'purpose' => 'forgetpassword',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
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
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|digits:6',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Step 2: Find user by email + OTP
            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP',
                ], 400);
            }

            //OPTIONAL: Check OTP expiry if otp_created_at is stored
            if (now()->diffInMinutes($user->otp_created_at) > 5) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP expired',
                ], 400);
            }

            // Step 3: Update password
            $user->password = Hash::make($request->password);
            $user->otp = null;
            $user->save();

            // Send notification for successful password reset
            $user->notify(new UserNotification(
                'Password Reset Successful',
                'Your password has been reset successfully.',
                ['user_id' => $user->id, 'type' => 'password_reset_success']
            ));

            return response()->json([
                'status' => true,
                'message' => 'Password reset successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout API
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $request->user()->currentAccessToken()->delete();

            // Send notification for logout
            $user->notify(new UserNotification(
                'Logout Successful',
                'You have successfully logged out from your account.',
                ['user_id' => $user->id, 'type' => 'logout']
            ));

            return response()->json([
                'status' => true,
                'message' => 'Logged out successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
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
                'status' => true,
                'message' => 'Profile retrieved successfully',
                'data' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'is_verified' => $user->is_verified,
                    'created_at' => $user->created_at,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update Profile API
     */


    /**
     * Create Password API
     */
    public function createPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)
                ->where(function ($query) use ($request) {
                    $query->where('otp', $request->otp)
                        ->orWhere('purpose', $request->purpose);
                })
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP or email',
                ], 400);
            }

            // Update password and clear OTP
            $user->password = Hash::make($request->password);
            $user->otp = null;
            $user->purpose = null;
            $user->save();

            // Send notification for password creation
            $user->notify(new UserNotification(
                'Password Created',
                'Your password has been created successfully.',
                ['user_id' => $user->id, 'type' => 'password_created']
            ));

            return response()->json([
                'status' => true,
                'message' => 'Password created successfully',
                'data' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Change Password API
     */
    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Step 1: Validation
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required|string|min:6|different:old_password',
            ], [
                'new_password.different' => 'Old and new password cannot be same.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            // Step 2: Check old password
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect old password',
                ], 400);
            }

            // Step 3: Update to new password
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Send notification for password change
            $user->notify(new UserNotification(
                'Password Changed',
                'Your account password has been changed successfully.',
                ['user_id' => $user->id, 'type' => 'password_changed']
            ));

            return response()->json([
                'status' => true,
                'message' => 'Password changed successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            // 🔐 Auth middleware se current user
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'full_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|unique:users,phone,' . $user->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $data = [];

            if ($request->filled('full_name')) {
                $data['full_name'] = $request->full_name;
            }

            if ($request->filled('email')) {
                $data['email'] = $request->email;
            }

            if ($request->filled('phone')) {
                $data['phone'] = $request->phone;
            }

            if (empty($data)) {
                return response()->json([
                    'code' => 400,
                    'message' => 'No data provided'
                ], 400);
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update($data);

            $updatedUser = DB::table('users')->where('id', $user->id)->first();

            // Send and Save notification for Profile Update
            $user->notify(new UserNotification(
                'Profile Updated',
                'Your profile details have been successfully updated.',
                ['user_id' => $user->id, 'type' => 'profile_updated']
            ));

            return response()->json([
                'code' => 200,
                'message' => 'Profile updated successfully',
                'data' => $updatedUser
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Account API
     */
    public function deleteAccount(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Delete user's tokens
            $user->tokens()->delete();

            // Delete the user
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'Account deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}