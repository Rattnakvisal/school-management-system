<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_class_id',
        'teacher_id',
        'name',
        'code',
        'study_time',
        'study_start_time',
        'study_end_time',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'teacher_id' => 'integer',
            'study_time' => 'string',
            'study_start_time' => 'string',
            'study_end_time' => 'string',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'school_class_id', 'school_class_id')->where('role', 'student');
    }

    public function studySchedules(): HasMany
    {
        return $this->hasMany(SubjectStudyTime::class)
            ->orderBy('sort_order')
            ->orderBy('start_time');
    }
}
