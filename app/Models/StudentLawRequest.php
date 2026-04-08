<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLawRequest extends Model
{
    protected $fillable = [
        'student_id',
        'school_class_id',
        'subject_id',
        'teacher_id',
        'law_type',
        'subject',
        'subject_time',
        'requested_for',
        'reason',
        'status',
        'teacher_note',
        'reviewed_at',
    ];

    /**
     * Casts
     *
     * @var array<string,string>
     */
    protected $casts = [
        'requested_for' => 'date',
        'subject_id' => 'integer',
        'teacher_id' => 'integer',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subjectRecord(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
