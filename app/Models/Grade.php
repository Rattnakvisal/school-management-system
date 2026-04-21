<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'subject_id',
        'title',
        'score',
        'max_score',
        'grade_letter',
        'remarks',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'teacher_id' => 'integer',
            'student_id' => 'integer',
            'subject_id' => 'integer',
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'graded_at' => 'date',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
