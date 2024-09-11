<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Rules;

use AshAllenDesign\LaravelExchangeRates\Rules\ValidCurrency;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Illuminate\Support\Facades\Validator;

final class ValidCurrencyTest extends TestCase
{
    /**
     * @test
     *
     * @testWith ["GBP"]
     *           ["BYR"]
     *           ["BYN"]
     *           ["VES"]
     */
    public function validator_returns_true_if_the_currency_is_valid(string $currency): void
    {
        $testData = [
            'currency' => $currency,
        ];

        $rules = [
            'currency' => new ValidCurrency,
        ];

        $validator = Validator::make($testData, $rules);
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function validator_returns_false_if_the_currency_is_invalid(): void
    {
        $testData = [
            'currency' => 'INVALID',
        ];

        $rules = [
            'currency' => new ValidCurrency,
        ];

        $validator = Validator::make($testData, $rules);
        $this->assertTrue($validator->fails());
    }
}
