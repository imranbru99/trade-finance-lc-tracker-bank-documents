<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'effective_date' => 'date',
    ];

    public static function convert(float $amount, string $from, string $to, ?string $date = null): float
    {
        if ($from === $to) {
            return $amount;
        }

        $date = $date ?? now()->toDateString();

        $rate = static::where('base_currency', $from)
            ->where('target_currency', $to)
            ->where('effective_date', '<=', $date)
            ->latest('effective_date')
            ->value('exchange_rate');

        if ($rate) {
            return round($amount * (float) $rate, 2);
        }

        // Check inverse
        $inverseRate = static::where('base_currency', $to)
            ->where('target_currency', $from)
            ->where('effective_date', '<=', $date)
            ->latest('effective_date')
            ->value('exchange_rate');

        if ($inverseRate && (float) $inverseRate > 0) {
            return round($amount / (float) $inverseRate, 2);
        }

        return $amount;
    }
}
