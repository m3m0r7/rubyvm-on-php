<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Essential\EntityInterface;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

class Number extends Entity implements EntityInterface
{
    public function __construct(NumberSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function testValue(): bool
    {
        return (bool) $this->symbol->valueOf();
    }

    public function xor(RubyClass $object): NumberSymbol
    {
        return new NumberSymbol(
            $this->symbol->valueOf() ^ $object->entity->symbol()->valueOf(),
        );
    }

    public function power(RubyClass $object): NumberSymbol
    {
        return new NumberSymbol(
            $this->symbol->valueOf() ** $object->entity->symbol()->valueOf(),
        );
    }

    public function rightShift(RubyClass $object): NumberSymbol
    {
        return new NumberSymbol(
            $this->symbol->valueOf() >> $object->entity->symbol()->valueOf(),
        );
    }

    public function compareStrictEquals(RubyClass $object): BooleanSymbol
    {
        return new BooleanSymbol(
            $this->symbol->valueOf() === $object->entity->symbol()->valueOf(),
        );
    }

    public function toInt(): self
    {
        return clone $this;
    }

    public function toFloat(): FloatSymbol
    {
        return new FloatSymbol(
            (float) $this->symbol->valueOf(),
        );
    }

    public function toString(): SymbolInterface
    {
        return new StringSymbol(
            string: (string) $this,
        );
    }
}
