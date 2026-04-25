<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionEvent extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'location',
        'audience',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisibleToRole($query, string $role)
    {
        $role = strtolower(trim($role));

        return $query->where('is_active', true)
            ->where(function ($inner) use ($role) {
                $inner->where('audience', 'all')
                    ->orWhere('audience', $role);
            });
    }
}
