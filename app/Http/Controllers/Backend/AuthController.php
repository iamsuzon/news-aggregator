<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validated->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['token' => $user->createToken('API Token')->plainTextToken], Response::HTTP_UNAUTHORIZED);
    }

    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validated->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json(['token' => $user->createToken('API Token')->plainTextToken]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()]);
        }

        $data = $validated->validated();
        $result = PasswordResetService::sendResentToken($data['email']);

        if ($result) {
            return response()->json(['message' => 'Reset token sent '], Response::HTTP_OK);
        }

        return response()->json(['error' => 'Failed to send reset token'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function reset(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validated->validated();
        $result = PasswordResetService::reset($data);

        return response()->json(['message' => $result['message']], $result['status']);
    }
}
