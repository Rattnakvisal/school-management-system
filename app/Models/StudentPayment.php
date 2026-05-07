<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPayment extends Model
{
    use HasFactory;

    public const STATUSES = ['paid', 'partial', 'pending', 'overdue', 'waived'];

    public const PAYMENT_METHODS = ['cash', 'qr_code', 'other'];

    protected $fillable = [
        'student_id',
        'amount',
        'discount_amount',
        'payment_date',
        'due_date',
        'status',
        'payment_method',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'payment_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function getMethodLabelAttribute(): string
    {
        $method = trim((string) $this->payment_method);

        return $method !== '' ? self::methodLabel($method) : 'Not specified';
    }

    public function getNetAmountAttribute(): float
    {
        return max((float) $this->amount - (float) $this->discount_amount, 0);
    }

    public static function methodLabel(string $method): string
    {
        return match ($method) {
            'qr_code' => 'QR Code',
            default => ucfirst(str_replace('_', ' ', $method)),
        };
    }
}
