<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class FloatSymbol implements SymbolInterface
{
    public function __construct(
        public readonly float $number,
    ) {
    }

    public function __toString(): string
    {
        $hasFraction = str_contains((string) $this->number, '.');
        if ($hasFraction) {
            return (string) rtrim(
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
