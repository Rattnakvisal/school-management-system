<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'marked_by',
        'attendance_date',
        'status',
        'remark',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'teacher_id' => 'integer',
            'marked_by' => 'integer',
            'attendance_date' => 'date',
            'checked_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function markedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
