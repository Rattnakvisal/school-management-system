<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherLawRequest extends Model
{
    protected $fillable = [
        'teacher_id',
        'law_type',
        'subject',
        'requested_for',
        'reason',
        'status',
        'admin_note',
        'reviewed_at',
    ];

    /**
     * Casts
     *
     * @var array<string,string>
     */
    protected $casts = [
        'requested_for' => 'date',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
