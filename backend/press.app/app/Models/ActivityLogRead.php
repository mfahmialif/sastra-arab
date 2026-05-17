<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLogRead extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'activity_log_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function log(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class, 'activity_log_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
