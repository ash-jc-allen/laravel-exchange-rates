<?php

namespace AshAllenDesign\LaravelExchangeRates\Rules;

use AshAllenDesign\LaravelExchangeRates\Classes\Currency;
use Illuminate\Contracts\Validation\Rule;

class ValidCurrency implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return (new Currency())->isAllowableCurrency($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute must be a valid exchange rates currency.';
    }
}
