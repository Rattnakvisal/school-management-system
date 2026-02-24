<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'school_class_id',
        'attendance_date',
        'status',
        'remark',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'teacher_id' => 'integer',
            'school_class_id' => 'integer',
            'attendance_date' => 'date',
            'checked_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }
}

