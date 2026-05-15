<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::firstWhere('email', $request->email);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Revoke old tokens for this user
        ApiToken::where('user_id', $user->id)->delete();

        $plainToken = Str::random(64);

        ApiToken::create([
            'user_id'    => $user->id,
            'token'      => hash('sha256', $plainToken),
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json([
            'token' => $plainToken,
            'user'  => $this->formatUser($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($this->formatUser($request->user()));
    }

    protected function formatUser($user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'phone'      => $user->phone,
            'full_name'  => $user->full_name ?: $user->name,
            'avatar_url' => null,
            'is_active'  => true,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            ApiToken::where('token', hash('sha256', $token))->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:50',
        ]);

        $user = $request->user();

        if ($request->has('full_name')) {
            $user->full_name = $request->full_name;
            $user->name = $request->full_name; // Keep name in sync
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated',
            'user'    => $this->formatUser($user),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }
}
