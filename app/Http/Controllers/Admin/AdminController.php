<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class AdminController extends Controller
{
    public function verifyUser($userId)
    {
        // Ambil data user berdasarkan ID
        $user = User::findOrFail($userId);

        // Cek jika user sudah di-verify
        if ($user->status === 'verified') {
            return response()->json(['message' => 'User already verified.'], 400);
        }

        // Generate License Key
        $licenseKey = Str::uuid(); // Membuat unique license key

        // Path untuk menyimpan QR Code
        $qrCodePath = "qrcodes/$licenseKey.png";

        // Generate QR Code menggunakan PHP QR Code
        Builder::create()
            ->writer(new PngWriter())
            ->data($licenseKey)
            ->size(300)
            ->margin(10)
            ->build()
            ->saveToFile(public_path($qrCodePath));

        // Update user status, simpan QR Code, dan ubah status menjadi verified
        $user->update([
            'status' => 'verified',
            'qr_code' => $qrCodePath,
        ]);

        return response()->json([
            'message' => 'User verified and QR Code generated successfully.',
            'qr_code_path' => asset($qrCodePath),
        ]);
    }
}
