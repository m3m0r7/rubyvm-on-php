<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\YARV\Essential\Symbol\ClassSymbol;

class Class_ extends Entity implements EntityInterface
{
    public function __construct(ClassSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = null): self
    {
        return new self(new ClassSymbol($value));
    }
}
