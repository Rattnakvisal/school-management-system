<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'title',
        'description',
        'due_at',
    ];

    protected function casts(): array
    {
        return [
            'teacher_id' => 'integer',
            'subject_id' => 'integer',
            'due_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assignment_student', 'assignment_id', 'student_id')
            ->withTimestamps();
    }
}
