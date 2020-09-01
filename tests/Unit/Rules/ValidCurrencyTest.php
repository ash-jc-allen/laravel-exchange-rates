<?php

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Rules;

use AshAllenDesign\LaravelExchangeRates\Rules\ValidCurrency;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Illuminate\Support\Facades\Validator;

class ValidCurrencyTest extends TestCase
{
    /** @test */
    public function validator_returns_true_if_the_currency_is_valid()
    {
        $testData = [
            'currency' => 'GBP',
        ];

        $rules = [
            'currency' => new ValidCurrency,
        ];

        $validator = Validator::make($testData, $rules);
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function validator_returns_false_if_the_currency_is_invalid()
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
