<?php

namespace Tests\Unit;

use App\Models\CurrencyRate;
use Tests\TestCase;

class CurrencyRateTest extends TestCase
{
    public function test_convert_uses_direct_exchange_rate(): void
    {
        CurrencyRate::create([
            'base_currency' => 'USD',
            'target_currency' => 'BDT',
            'exchange_rate' => 110.5,
            'effective_date' => now()->subDay()->toDateString(),
        ]);

        $converted = CurrencyRate::convert(100, 'USD', 'BDT');

        $this->assertSame(11050.0, $converted);
    }

    public function test_convert_returns_same_amount_when_currencies_match(): void
    {
        $this->assertSame(42.5, CurrencyRate::convert(42.5, 'EUR', 'EUR'));
    }
}
