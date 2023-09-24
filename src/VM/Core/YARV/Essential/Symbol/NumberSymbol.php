<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class NumberSymbol implements SymbolInterface, \Stringable
{
    public function __construct(
        private readonly int $number,

        // @phpstan-ignore-next-line
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
