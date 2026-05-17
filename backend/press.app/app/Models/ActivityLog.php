<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'module',
        'action',
        'subject_type',
        'subject_id',
        'subject_title',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ActivityLogRead::class);
    }

    public function scopeUnreadFor($query, User $user)
    {
        return $query->whereDoesntHave('reads', fn ($readQuery) => $readQuery->where('user_id', $user->id));
    }
}
