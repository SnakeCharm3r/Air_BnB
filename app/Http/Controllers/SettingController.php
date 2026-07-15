<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Throwable;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getInstance();
        
        // Get active users (logged in within last 15 minutes)
        $fifteenMinutesAgo = now()->subMinutes(15);
        $activeUsers = User::where('last_login_at', '>=', $fifteenMinutesAgo)
            ->where('status', 'active')
            ->count();
        
        // Get locked/failed users
        $lockedUsers = User::where('status', 'locked')->count();
        $failedAttempts = User::sum('failed_attempts');
        
        // Get total users
        $totalUsers = User::count();
        
        // Get recent failed attempts from users
        $recentFailedLogins = User::where('failed_attempts', '>', 0)
            ->select('id', 'name', 'email', 'failed_attempts', 'locked_until', 'status')
            ->get();
        
        return view('settings.index', compact(
            'settings',
            'activeUsers',
            'lockedUsers',
            'failedAttempts',
            'totalUsers',
            'recentFailedLogins'
        ));
    }

    public function update(Request $request)
    {
        try {
            $settings = Setting::getInstance();
            
            $validated = $request->validate([
                'lodge_name' => 'required|string|max:255',
                'contact_email' => 'nullable|email',
                'contact_phone' => 'nullable|string|max:20',
                'contact_address' => 'nullable|string',
                'currency' => 'nullable|string|max:10',
                'owner_email' => 'nullable|email',
                'max_login_attempts' => 'nullable|integer|min:1|max:10',
                'lockout_duration' => 'nullable|integer|min:1|max:1440',
                'two_factor_auth' => 'nullable|boolean',
                'session_timeout' => 'nullable|integer|min:1|max:168',
                'audit_logging' => 'nullable|boolean',
                'lodge_logo' => 'nullable|image|max:2048',
                'login_logo' => 'nullable|image|max:2048',
                'favicon' => 'nullable|image|max:1024',
            ]);

            // Handle checkbox booleans (unchecked checkboxes don't submit)
            $validated['two_factor_auth'] = $request->boolean('two_factor_auth');
            $validated['audit_logging'] = $request->boolean('audit_logging');

            // Handle notification preferences
            $validated['notification_settings'] = [
                'checkout_reminders' => $request->boolean('checkout_reminders'),
                'low_inventory_alerts' => $request->boolean('low_inventory_alerts'),
                'maintenance_due' => $request->boolean('maintenance_due'),
                'device_status_changes' => $request->boolean('device_status_changes'),
            ];

            // Handle file uploads
            if ($request->hasFile('lodge_logo')) {
                $path = $request->file('lodge_logo')->store('logos', 'public');
                $validated['lodge_logo'] = $path;
                Log::info('Lodge logo uploaded', ['path' => $path, 'user' => auth()->user()->id]);
            }

            if ($request->hasFile('login_logo')) {
                $path = $request->file('login_logo')->store('logos', 'public');
                $validated['login_logo'] = $path;
                Log::info('Login logo uploaded', ['path' => $path, 'user' => auth()->user()->id]);
            }

            if ($request->hasFile('favicon')) {
                $path = $request->file('favicon')->store('logos', 'public');
                $validated['favicon'] = $path;
                Log::info('Favicon uploaded', ['path' => $path, 'user' => auth()->user()->id]);
            }

            $settings->update($validated);
            Log::info('Settings updated', ['user' => auth()->user()->id, 'lodge_name' => $validated['lodge_name']]);

            return redirect()->route('settings')->with('success', 'Settings updated successfully');
        } catch (Throwable $e) {
            Log::error('Failed to update settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->user()->id ?? 'guest',
                'request' => $request->all(),
            ]);
            return redirect()->route('settings')->with('error', 'Failed to save settings. Please try again.');
        }
    }

    public function unlockUser(User $user)
    {
        $user->unlock();
        return redirect()->route('settings')->with('success', 'User unlocked successfully');
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->status === 'active') {
            $user->status = 'locked';
        } else {
            $user->unlock();
        }
        $user->save();
        
        return redirect()->route('settings')->with('success', 'User status updated');
    }
}
