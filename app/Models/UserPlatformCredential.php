<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlatformCredential extends Model
{
    protected $fillable = [
        'user_id',
        'platform_id',
        'username',
        'password',
        'location',
        'access_token',
        'refresh_token',
    ];

    protected $casts = [
        'password' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
