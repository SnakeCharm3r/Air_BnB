<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::select('id', 'name', 'email', 'role', 'phone', 'full_name', 'created_at')->get());
    }

    public function show($id)
    {
        $user = User::find($id);
        if (! $user) return response()->json(['message' => 'User not found'], 404);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|string',
            'phone'    => 'nullable|string',
            'full_name'=> 'nullable|string',
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (! $user) return response()->json(['message' => 'User not found'], 404);

        $data = $request->validate([
            'name'     => 'sometimes|string',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'role'     => 'sometimes|string',
            'phone'    => 'nullable|string',
            'full_name'=> 'nullable|string',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return response()->json($user);
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
