<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'authentication_curl_template',
        'auth_token_key',
        'refresh_curl_template',
        'refresh_token_key',
        'related_auth_curl',
        'calendar_api_curl_template',
    ];

    public function actions()
    {
        return $this->hasMany(PlatformAction::class);
    }
}
