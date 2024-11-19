<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Ganti ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable // Ganti dari Model ke Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone_number', 'desa_id', 'province_id', 'regency_id', 'district_id', 'village_id', 'status', 'qr_code', 'license_key',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            if ($user->role === 'kepala_desa') {
                $desa = Desa::find($user->desa_id);
                if ($desa) {
                    $desa->update(['kepala_desa_id' => null]);
                }
            }
        });
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    protected $hidden = [
        'password', 'remember_token',
    ];
}
