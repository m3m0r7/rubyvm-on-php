<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class NumberSymbol implements SymbolInterface
{
    public function __construct(
        public readonly int $number,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->number;
    }
}
