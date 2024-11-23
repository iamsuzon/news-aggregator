<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetService
{
    public static function sendResentToken(string $email): bool
    {
        $token = rand(111111, 999999);

        try {
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            Mail::send('emails.reset-password', ['token' => $token], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Password Reset Request');
            });

            return true;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    public static function reset($data): array
    {
        $email = $data['email'];
        $token = $data['token'];
        $password = $data['password'];

        $resetRecord = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$resetRecord) {
            return ['message' => 'Invalid token or email', 'status' => 400];
        }

        // Verify the token and check its expiration
        if (!Hash::check($token, $resetRecord->token) || now()->diffInMinutes($resetRecord->created_at) > 60) {
            return ['message' => 'Invalid or expired token', 'status' => 400];
        }

        User::where('email', $email)->update(['password' => Hash::make($password)]);
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return ['message' => 'Password reset successfully', 'status' => 200];
    }
}
