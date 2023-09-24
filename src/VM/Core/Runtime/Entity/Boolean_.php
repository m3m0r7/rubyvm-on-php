<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;

class Boolean_ extends Entity implements EntityInterface
{
    public function __construct(BooleanSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function toString(): String_
    {
        return String_::createBy(
            (string) $this,
        );
    }

    public function testValue(): bool
    {
        return $this->symbol->valueOf();
    }

    public static function createBy(mixed $value = true): self
    {
        return new self(new BooleanSymbol($value));
    }
}
