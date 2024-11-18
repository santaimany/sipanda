<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
        ]);

        $user = User::findOrFail($id);

        if ($user->status !== 'pending') {
            return response()->json(['message' => 'User status can only be updated if it is pending.'], 400);
        }

        $user->status = $request->status;
        $user->save();

        return new UserResource($user->load('desa'));
    }
}
