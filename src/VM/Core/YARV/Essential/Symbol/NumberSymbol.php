<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Runtime\RubyClass;

class NumberSymbol implements SymbolInterface
{
    public function __construct(
        private readonly int $number,
        private readonly bool $isFixed = false,
    ) {}

    public function valueOf(): int
    {
        return $this->number;
    }

    public function __toString(): string
    {
        return (string) $this->number;
    }
}
