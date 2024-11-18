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
        if ($user->status === 'approved') {
            return response()->json(['message' => 'User already approved.'], 400);
        }

        if ($action === 'approve') {
            // Generate License Key
            $licenseKey = Str::uuid();

            // Path untuk menyimpan QR Code
            $qrCodePath = "qrcodes/$licenseKey.png";

            // Generate QR Code
            Builder::create()
                ->writer(new PngWriter())
                ->data($licenseKey)
                ->size(300)
                ->margin(10)
                ->build()
                ->saveToFile(storage_path("app/$qrCodePath"));

            // Update status, QR Code, dan License Key
            $user->update([
                'status' => 'pending_qr',
                'qr_code' => $qrCodePath,
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
}
