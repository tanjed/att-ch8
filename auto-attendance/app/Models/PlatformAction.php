<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformAction extends Model
{
    protected $fillable = [
        'platform_id',
        'name',
        'api_curl_template',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
