<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();

        if ($request->has('full_name')) {
            $user->full_name = $request->full_name;
            $user->name = $request->full_name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password updated successfully');
    }
}
