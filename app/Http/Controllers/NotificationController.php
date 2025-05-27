<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
   
    public function getUserNotifications(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(), // Waktu dalam format manusia
                ];
            });

        return response()->json([
            'message' => 'Notifikasi berhasil diambil.',
            'data' => $notifications,
        ]);
    }

    // Tandai notifikasi sebagai sudah dibaca
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notifikasi berhasil ditandai sebagai dibaca.']);
    }
}
