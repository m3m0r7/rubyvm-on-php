<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class FloatSymbol implements SymbolInterface, \Stringable
{
    public function __construct(
        private readonly float $number,
    ) {}

    public function valueOf(): float
    {
        return $this->number;
    }

    public function __toString(): string
    {
        $hasFraction = str_contains((string) $this->number, '.');
        if ($hasFraction) {
            return rtrim(
                sprintf(
                    '%.16f',
                    $this->number
                ),
                '0'
            );
        }

        return "{$this->number}.0";
    }
}
