<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use App\Http\Controllers\Controller;

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
            $qrCodePath = "qrcodes/$licenseKey.png";
            $qrCodeFullPath = public_path("storage/$qrCodePath");

            // Generate QR Code
            Builder::create()
                ->writer(new PngWriter())
                ->data($licenseKey)
                ->size(300)
                ->margin(10)
                ->build()
                ->saveToFile($qrCodeFullPath);

            // Update status, QR Code, dan License Key
            $user->update([
                'status' => 'pending_qr',
                'qr_code' => "storage/$qrCodePath",
                'license_key' => $licenseKey,
            ]);

            return response()->json([
                'message' => 'User approved and QR Code generated.',
                'qr_code_path' => asset("storage/$qrCodePath"),
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
