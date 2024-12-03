<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\PngWriter;

class AdminController extends Controller
{
    public function verifyUser($id, $action)
    {
        $user = User::findOrFail($id);

        // Cek apakah status sudah disetujui
        if ($user->status === 'verified') {
            return response()->json(['message' => 'User already approved.'], 400);
        }

        if ($action === 'approve') {
            // Generate License Key
            $licenseKey = Str::uuid();

            // Path untuk menyimpan QR Code di public/storage/qrcodes
            $tempPath = "/tmp/$licenseKey.png";
            // $qrCodePath = "app/qrcodes/$licenseKey.png";
            // $qrCodeFullPath = public_path("storage/$qrCodePath");

            // Generate QR Code
            Builder::create()
                ->writer(new PngWriter())
                ->data($licenseKey)
                ->size(300)
                ->margin(10)
                ->build()
                ->saveToFile($tempPath);
                // ->saveToFile($qrCodeFullPath);

            // Direktori publik untuk menyimpan QR Code
$publicDir = public_path("storage/qrcodes");
if (!is_dir($publicDir)) {
    mkdir($publicDir, 0755, true); // Buat direktori jika belum ada
}
    // Salin file dari /tmp ke public/storage
$publicPath = "$publicDir/$licenseKey.png";
copy($tempPath, $publicPath);

// URL file yang dapat diakses
$qrCodeUrl = asset("storage/qrcodes/$licenseKey.png");

    // Update status dan License Key di database
    $user->update([
        'status' => 'pending_qr',
        'license_key' => $licenseKey,
        'qr_code' => "storage/qrcodes/$licenseKey.png",
    ]);

    // Kembalikan URL file
    return response()->json([
        'message' => 'User approved and QR Code generated.',
        'qr_code_url' => $qrCodeUrl,
    ]);
        }

        if ($action === 'reject') {
            $user->update(['status' => 'rejected']);
            return response()->json(['message' => 'User rejected.']);
        }

        return response()->json(['message' => 'Invalid action.'], 400);
    }

    public function getPendingUsers()
    {
        $users = User::where('status', 'pending')->get();
        return response()->json(['data' => $users]);
    }
}
