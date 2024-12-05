<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Get user settings.
     */
    public function getSettings()
    {
        $user = Auth::user();

        return response()->json([
            'message' => 'User settings retrieved successfully.',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Update user settings.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::users();
    
        $validated = $request->validate([
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        // Update email if provided
        if ($request->has('email') && $request->email) {
            $user->email = $validated['email'];
        }
    
        // Update password if provided
        if ($request->has('password') && $request->password) {
            $user->password = Hash::make($validated['password']);
        }
    
        // Update profile picture if provided
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
    
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }
    
        // Save user changes
        $user->save();
    
        return response()->json([
            'message' => 'User settings updated successfully.',
            'data' => [
                'email' => $user->email,
                'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
            ],
        ]);
    }
    
}
