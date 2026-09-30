<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    public static function generateTransactionNumber(): string
    {
        $prefix = 'TRX-' . now()->format('Ymd') . '-';
        $lastTransaction = static::where('transaction_number', 'like', $prefix . '%')
            ->orderByDesc('transaction_number')
            ->value('transaction_number');

        $lastSequence = $lastTransaction ? (int) substr($lastTransaction, -4) : 0;

        return $prefix . str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
    }

    protected $fillable = [
        'user_id',
        'transaction_number',
        'transaction_date',
        'total_amount',
        'payment_amount',
        'change_amount',
        'status',
        'canceled_at',
        'canceled_by',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'canceled_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'payment_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canceledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function isCancelled(): bool
    {
        return $this->status === 'CANCELLED';
    }
}