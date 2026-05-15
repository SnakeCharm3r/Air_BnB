<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is locked
        if ($user && $user->isLocked()) {
            $lockoutTime = $user->locked_until ? $user->locked_until->diffForHumans() : 'indefinitely';
            return back()->withErrors(['email' => 'Account is locked. Try again ' . $lockoutTime])->withInput();
        }

        // Verify credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Record failed attempt
            if ($user) {
                $settings = Setting::getInstance();
                $user->recordFailedAttempt($settings->max_login_attempts, $settings->lockout_duration);
            }
            
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        // Successful login - reset failed attempts
        $user->resetFailedAttempts();

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
