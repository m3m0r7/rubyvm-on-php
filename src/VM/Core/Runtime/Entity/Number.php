<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

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

    public function xor(RubyClass $object): Number
    {
        return new Number(new NumberSymbol(
            $this->symbol->valueOf() ^ $object->entity->symbol()->valueOf(),
        ));
    }

    public function power(RubyClass $object): Number
    {
        return new Number(new NumberSymbol(
            $this->symbol->valueOf() ** $object->entity->symbol()->valueOf(),
        ));
    }

    public function rightShift(RubyClass $object): Number
    {
        return new Number(new NumberSymbol(
            $this->symbol->valueOf() >> $object->entity->symbol()->valueOf(),
        ));
    }

    public function compareStrictEquals(RubyClass $object): Boolean_
    {
        return new Boolean_(new BooleanSymbol(
            $this->symbol->valueOf() === $object->entity->symbol()->valueOf(),
        ));
    }

    public function toInt(): self
    {
        return clone $this;
    }

    public function toFloat(): Float_
    {
        return new Float_(new FloatSymbol(
            (float) $this->symbol->valueOf(),
        ));
    }

    public function toString(): String_
    {
        return new String_(new StringSymbol(
            string: (string) $this,
        ));
    }

    public static function createBy(mixed $value = 0): EntityInterface
    {
        return new self(new NumberSymbol($value));
    }
}
