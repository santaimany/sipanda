<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $user->update(['status' => 'approved']);

        return response()->json(['message' => 'User verified successfully.']);
    }
}
