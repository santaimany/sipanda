<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function verifyQrCode(Request $request)
    {
        $licenseKey = $request->input('license_key');

        // Cari user berdasarkan license key
        $user = User::where('license_key', $licenseKey)->first();


        if (!$user) {
            return response()->json(['message' => 'Invalid QR Code.'], 400);
        }

        if ($user->status !== 'pending_qr') {
            return response()->json(['message' => 'User already verified or in invalid state.'], 400);
        }

        // Update status menjadi approved
        $user->update(['status' => 'verified']);

        return response()->json(['message' => 'User verified successfully.']);
    }

    
    public function checkStatus(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Periksa apakah pengguna ada dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        // Cek status akun
        switch ($user->status) {
            case 'verified':
                return response()->json([
                    'status' => 'approved',
                    'message' => 'Akun Anda telah disetujui.',
                ], 200);

            case 'pending_qr':
                return response()->json([
                    'status' => 'pending_qr',
                    'message' => 'Akun Anda telah disetujui! Lakukan Verifikasi dengan Qr Code.',
                    'qr_code_url' => url("/qr-code/{$user->qr_code}"),
                ], 200);

            case 'pending':
                return response()->json([
                    'status' => 'pending',
                    'message' => 'Akun anda sedang menunggu persetujuan admin.',
                ], 200);

            case 'rejected':
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'Akun Anda telah ditolak oleh admin.',
                ], 200);

            default:
                return response()->json([
                    'status' => 'unknown',
                    'message' => 'Status akun Anda tidak diketahui.',
                ], 400);
        }
    }
}
