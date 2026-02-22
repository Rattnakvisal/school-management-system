<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'google_id',
        'provider',
        'avatar',
        'is_active',
        'school_class_id',
        'major_subject_id',
        'class_study_time_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'school_class_id' => 'integer',
            'major_subject_id' => 'integer',
            'class_study_time_id' => 'integer',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function majorSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'major_subject_id');
    }

    public function classStudyTime(): BelongsTo
    {
        return $this->belongsTo(ClassStudyTime::class, 'class_study_time_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'school_class_id', 'school_class_id');
    }

    public function taughtSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    public function getAvatarUrlAttribute(): string
    {
        $avatar = trim((string) $this->avatar);

        if ($avatar === '') {
            return $this->fallbackAvatarUrl();
        }

        if (Str::startsWith($avatar, ['http://', 'https://', '//', 'data:image/'])) {
            return $avatar;
        }

        $path = ltrim($avatar, '/');
        if (Str::startsWith($path, 'public/')) {
            $path = Str::after($path, 'public/');
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return $this->fallbackAvatarUrl();
    }

    protected function fallbackAvatarUrl(): string
    {
        $name = trim((string) ($this->name ?: 'User'));
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        if ($initials === '') {
            $initials = 'U';
        }

        $svg = sprintf(
            "<svg xmlns='http://www.w3.org/2000/svg' width='128' height='128' viewBox='0 0 128 128'><rect width='128' height='128' fill='#E0E7FF'/><text x='50%%' y='50%%' dominant-baseline='middle' text-anchor='middle' font-family='Arial,sans-serif' font-size='48' font-weight='700' fill='#4338CA'>%s</text></svg>",
            htmlspecialchars($initials, ENT_QUOTES, 'UTF-8')
        );

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    public function getFallbackAvatarUrlAttribute(): string
    {
        return $this->fallbackAvatarUrl();
    }

    public function getFormattedIdAttribute(): string
    {
        return str_pad((string) $this->id, 7, '0', STR_PAD_LEFT);
    }
}
