<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'desa_id',
        'status',
        'qr_code',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
