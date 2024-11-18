<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:kepala_desa,bapanas',
            'phone_number' => 'nullable|string|max:15',
            'desa' => 'required_if:role,kepala_desa|array',
            'desa.nama' => 'required_if:role,kepala_desa|string|max:100',
            'desa.provinsi' => 'required_if:role,kepala_desa|string|max:100',
            'desa.kabupaten' => 'required_if:role,kepala_desa|string|max:100',
            'desa.kecamatan' => 'required_if:role,kepala_desa|string|max:100',
            'desa.kelurahan' => 'required_if:role,kepala_desa|string|max:100',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Simpan data desa jika role adalah kepala_desa
        $desa_id = null;
        if ($request->role === 'kepala_desa') {
            $desa = Desa::create([
                'nama' => $request->input('desa.nama'),
                'provinsi' => $request->input('desa.provinsi'),
                'kabupaten' => $request->input('desa.kabupaten'),
                'kecamatan' => $request->input('desa.kecamatan'),
                'kelurahan' => $request->input('desa.kelurahan'),
            ]);
            $desa_id = $desa->id;
        }
    
        // Simpan user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone_number' => $request->phone_number,
            'desa_id' => $desa_id, // Akan null jika role bapanas
            'status' => 'pending', // Default pending
        ]);
    
        return new UserResource($user->load('desa'));
    }
    
}
