<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class NumberSymbol implements SymbolInterface
{
    public function __construct(
        public readonly int $number,
        public readonly bool $isFixed = false,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->number;
    }

    public function xor(NumberSymbol $symbol): self
    {
        return new self(
            $this->number ^ $symbol->number,
        );
    }

    public function power(NumberSymbol $symbol): self
    {
        return new NumberSymbol(
            $this->number ** $symbol->number,
        );
    }

    public function rightShift(NumberSymbol $symbol): self
    {
        return new NumberSymbol(
            $this->number >> $symbol->number,
        );
    }

    public function toInt(): self
    {
        return clone $this;
    }

    public function toString(): SymbolInterface
    {
        return new StringSymbol(
            string: "{$this->number}",
        );
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::FIXNUM,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }
}
