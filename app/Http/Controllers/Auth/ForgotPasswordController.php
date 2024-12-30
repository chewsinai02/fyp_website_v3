<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        try {
            // Get the email and normalize it
            $email = trim(strtolower($request->email));
            
            // Check if user exists (case-insensitive)
            $user = DB::table('users')
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if (!$user) {
                // Try to find similar email
                $similarUser = DB::table('users')
                    ->whereRaw('LOWER(email) LIKE ?', ['%' . explode('@', $email)[0] . '%'])
                    ->first();

                $message = 'We could not find a user with that email address.';
                if ($similarUser) {
                    $message .= ' Did you mean ' . $similarUser->email . '?';
                }

                return response()->json([
                    'status' => 'error',
                    'message' => $message
                ], 422);
            }

            // Delete any existing reset records
            DB::table('password_resets')
                ->where('email', $user->email)
                ->delete();

            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store the reset record first with used=1 (unused)
            DB::table('password_resets')->insert([
                'email' => $user->email,
                'otp' => $otp,
                'created_at' => Carbon::now(),
                'used' => 1  // 1 means unused
            ]);

            // Send email
            try {
                Mail::send('emails.forgot-password', ['otp' => $otp], function($message) use($user) {
                    $message->to($user->email)
                        ->subject('Password Reset OTP');
                });
            } catch (\Exception $e) {
                \Log::error('Mail sending failed', [
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);

                // Clean up the reset record if email fails
                DB::table('password_resets')
                    ->where('email', $user->email)
                    ->delete();

                throw new \Exception('Failed to send reset email. Please try again later.');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'We have emailed your password reset OTP!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $email = strtolower($request->email);

            // Add debug logging
            \Log::info('Verifying OTP', [
                'email' => $email,
                'otp' => $request->otp
            ]);

            $reset = DB::table('password_resets')
                ->whereRaw('LOWER(email) = ?', [$email])
                ->where('otp', $request->otp)
                ->where('used', 1)  // 1 means unused
                ->where('created_at', '>', Carbon::now()->subMinutes(15))
                ->first();

            // Add debug logging
            \Log::info('Reset record found', ['reset' => $reset]);

            if (!$reset) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired OTP'
                ], 400);
            }

            // Mark OTP as used (0)
            DB::table('password_resets')
                ->where('id', $reset->id)
                ->update(['used' => 0]);  // 0 means used

            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully',
                'data' => [
                    'email' => $reset->email
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('OTP verification error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify OTP. Please try again.'
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Get the user
            $user = DB::table('users')
                ->where('email', $request->email)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            // Check if new password is same as current password
            if (password_verify($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'New password cannot be the same as your current password'
                ], 422);
            }

            $reset = DB::table('password_resets')
                ->where('email', $request->email)
                ->where('used', 0)  // 0 means used (OTP verified)
                ->first();

            if (!$reset) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired reset request'
                ], 400);
            }

            DB::table('users')
                ->where('email', $request->email)
                ->update(['password' => bcrypt($request->password)]);

            // Clean up used reset records
            DB::table('password_resets')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Password has been reset successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Password reset error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reset password. Please try again.'
            ], 500);
        }
    }

    private function checkMailConfig()
    {
        \Log::info('Mail Configuration:', [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'username' => config('mail.mailers.smtp.username'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ]);
    }
}
