<?php
namespace App\Http\Controllers\User;

use App\Models\Pangan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PanganController extends Controller
{
    public function getPanganByUserDesa(Request $request)
    {
        $user = $request->user(); // Ambil user yang sedang login

        if (!$user->desa_id) {
            return response()->json(['message' => 'User tidak terkait dengan desa mana pun'], 403);
        }

        $pangans = Pangan::where('desa_id', $user->desa_id)->get();

        return response()->json([
            'message' => 'Data pangan ditemukan',
            'data' => $pangans,
        ]);
    }
}
