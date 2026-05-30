<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'phone', 'full_name', 'created_at', 'is_active')
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }

        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|string|min:6',
            'role'      => 'required|string',
            'phone'     => 'nullable|string',
            'full_name' => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }

        $data = $request->validate([
            'name'      => 'sometimes|string',
            'email'     => 'sometimes|email|unique:users,email,' . $id,
            'password'  => 'sometimes|string|min:6',
            'role'      => 'sometimes|string',
            'phone'     => 'nullable|string',
            'full_name' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        // Prevent deleting yourself
        if ($id == auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account');
        }

        User::where('id', $id)->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function toggleStatus($id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }

        // Prevent disabling yourself
        if ($id == auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot disable your own account');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'enabled' : 'disabled';
        return redirect()->route('users.index')->with('success', 'User ' . $status . ' successfully');
    }
}
