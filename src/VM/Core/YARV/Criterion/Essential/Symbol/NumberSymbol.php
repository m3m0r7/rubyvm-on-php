<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Essential\Symbol;

use RubyVM\VM\Core\Runtime\Object_;

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

    public function xor(Object_ $object): self
    {
        return new self(
            $this->number ^ $object->symbol->valueOf(),
        );
    }

    public function power(Object_ $object): self
    {
        return new NumberSymbol(
            $this->number ** $object->symbol->valueOf(),
        );
    }

    public function rightShift(Object_ $object): self
    {
        return new NumberSymbol(
            $this->number >> $object->symbol->valueOf(),
        );
    }

    public function compareStrictEquals(Object_ $object): BooleanSymbol
    {
        return new BooleanSymbol(
            $this->number === $object->symbol->valueOf(),
        );
    }

    public function toInt(): self
    {
        return clone $this;
    }

    public function toFloat(): FloatSymbol
    {
        return new FloatSymbol(
            (float) $this->number,
        );
    }

    public function toString(): SymbolInterface
    {
        return new StringSymbol(
            string: (string) $this,
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
