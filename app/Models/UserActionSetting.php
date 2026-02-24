<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActionSetting extends Model
{
    protected $fillable = [
        'user_id',
        'platform_action_id',
        'target_time',
        'latitude',
        'longitude',
        'is_active',
        'buffer_minutes',
        'next_execution_time',
        'weekly_off_days',
    ];

    protected $casts = [
        'weekly_off_days' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platformAction()
    {
        return $this->belongsTo(PlatformAction::class);
    }
}
