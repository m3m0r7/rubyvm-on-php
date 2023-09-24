<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class String_ extends Entity implements EntityInterface
{
    public function __construct(StringSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function testValue(): bool
    {
        return (bool) $this->symbol->valueOf();
    }

    public static function createBy(mixed $value = ''): self
    {
        return new self(new StringSymbol($value));
    }
}
