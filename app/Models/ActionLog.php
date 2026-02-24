<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    protected $table = 'action_logs';

    protected $fillable = [
        'user_id',
        'platform_action_id',
        'executed_at',
        'status',
        'response',
    ];

    public function platformAction()
    {
        return $this->belongsTo(PlatformAction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
