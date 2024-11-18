<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:100',
        'email' => 'required|string|email|max:100|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|in:kepala_desa,bapanas',
        'phone_number' => 'nullable|string|max:15',
        'province_id' => 'required|numeric',
        'regency_id' => 'required|numeric',
        'district_id' => 'required|numeric',
        'village_id' => 'required|numeric',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Cek apakah desa sudah memiliki kepala desa
    if ($request->role === 'kepala_desa') {
        $existingHead = User::where('village_id', $request->village_id)
            ->where('role', 'kepala_desa')
            ->exists();

        if ($existingHead) {
            return response()->json([
                'message' => 'Desa ini sudah memiliki kepala desa.',
            ], 400);
        }
    }

    // Simpan user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'phone_number' => $request->phone_number,
        'province_id' => $request->province_id,
        'regency_id' => $request->regency_id,
        'district_id' => $request->district_id,
        'village_id' => $request->village_id,
        'status' => 'pending',
    ]);

    return response()->json([
        'message' => 'Registration successful. Waiting for approval.',
        'user' => $user,
    ]);
}
    
}
