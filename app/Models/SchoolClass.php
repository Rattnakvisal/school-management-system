<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'section',
        'room',
        'study_time',
        'study_start_time',
        'study_end_time',
        'capacity',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
            'study_time' => 'string',
            'study_start_time' => 'string',
            'study_end_time' => 'string',
        ];
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    public function studySchedules(): HasMany
    {
        return $this->hasMany(ClassStudyTime::class)
            ->orderBy('sort_order')
            ->orderBy('start_time');
    }

    public function getDisplayNameAttribute(): string
    {
        $name = trim((string) $this->name);
        $section = trim((string) ($this->section ?? ''));

        if ($section === '') {
            return $name;
        }

        return $name . ' - ' . $section;
    }
}
