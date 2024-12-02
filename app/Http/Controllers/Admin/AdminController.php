<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Log;
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

            // Update status, QR Code, dan License Key
            $user->update([
                'status' => 'pending_qr',
                'qr_code' => "storage/$tempPath",
                // 'qr_code' => "storage/$qrCodePath",
                'license_key' => $licenseKey,
            ]);

            return response()->json([
                'message' => 'User approved and QR Code generated.',
                'qr_code_path' => asset("storage/$tempPath"),
                // 'qr_code_path' => asset("storage/$qrCodePath"),
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
