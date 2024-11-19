<?php

namespace App\Http\Controllers\auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Cek jika kredensial salah
        if (!$user || !Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Cek jika status user bukan 'approved'
        if ($user->status === 'pending') {
            return response()->json(['message' => 'Akun anda masih belum dikonfirmasi'], 403);
        } else if ($user->status === 'rejected') {
            return response()->json(['message' => 'Permintaan pembuatan akun anda ditolak'], 403);
        } else if ($user->status === 'pending_qr') {
            return response()->json(['message' => 'Akun anda belum terverifikasi oleh sistem'], 403);
        }

        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // Tentukan redirection berdasarkan role
        $dashboard = '';
        switch ($user->role) {
            case 'kepala_desa':
                $dashboard = '/dashboard/kepala_desa';
                break;
            case 'bapanas':
                $dashboard = '/dashboard/bapanas';
                break;
            case 'admin':
                $dashboard = '/dashboard/admin';
                break;
            default:
                return response()->json(['message' => 'Unauthorized role'], 403);
        }

        // Return data dengan redirection dan token
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'dashboard' => $dashboard,
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus semua token yang terkait dengan user yang sedang login
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
