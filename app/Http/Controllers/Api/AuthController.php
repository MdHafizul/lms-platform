<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Support\Exceptions\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string',
        ]);

        try {
            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone_number' => $validated['phone_number'] ?? null,
            ]);

            // Assign student role by default
            $user->assignRole('student');

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            // Record failed login
            if ($user) {
                $user->recordFailedLogin();
            }
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if account is locked
        if ($user->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Account is locked due to too many failed login attempts. Please try again later.',
            ], 429);
        }

        // Record successful login
        $user->recordSuccessfulLogin();

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user->load('lecturerProfile'),
                'token' => $token,
                'roles' => $user->getRoleNames(),
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('lecturerProfile'),
            'roles' => $request->user()->getRoleNames(),
        ], 200);
    }

    public function refresh(Request $request)
    {
        // Create new token
        $token = $request->user()->createToken('refreshed_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed',
            'data' => [
                'token' => $token,
                'user' => $request->user(),
            ],
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // TODO: Implement password reset token generation and email
        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to email',
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // TODO: Implement password reset logic with token verification
        return response()->json([
            'success' => true,
            'message' => 'Password reset successful',
        ], 200);
    }
}
