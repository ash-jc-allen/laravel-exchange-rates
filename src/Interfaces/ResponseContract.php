<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Interfaces;

interface ResponseContract
{
    public function rates(): array;

    public function raw(): mixed;

    public function get(string $key): mixed;
}
