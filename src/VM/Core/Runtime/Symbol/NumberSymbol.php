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

    public function calculateXOR(NumberSymbol $symbol): self
    {
        return new self(
            $this->number ^ $symbol->number,
        );
    }

    public function calculatePower(NumberSymbol $symbol): self
    {
        return new NumberSymbol(
            $this->number ** $symbol->number,
        );
    }

    public function calculateRightShift(NumberSymbol $symbol): self
    {
        return new NumberSymbol(
            $this->number >> $symbol->number,
        );
    }

    public function toInt(): self
    {
        return clone $this;
    }
}
