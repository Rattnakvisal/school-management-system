<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassStudyTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_class_id',
        'day_of_week',
        'period',
        'start_time',
        'end_time',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'string',
            'sort_order' => 'integer',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_study_times', 'class_study_time_id', 'user_id')
            ->withTimestamps();
    }
}
