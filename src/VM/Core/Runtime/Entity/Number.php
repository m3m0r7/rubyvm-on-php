<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;

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
        return Number::createBy(
            $this->symbol->valueOf() ^ $object->entity->symbol()->valueOf(),
        );
    }

    public function power(RubyClass $object): Number
    {
        return Number::createBy(
            $this->symbol->valueOf() ** $object->entity->symbol()->valueOf(),
        );
    }

    public function rightShift(RubyClass $object): Number
    {
        return Number::createBy(
            $this->symbol->valueOf() >> $object->entity->symbol()->valueOf(),
        );
    }

    public function compareStrictEquals(RubyClass $object): Boolean_
    {
        return Boolean_::createBy(
            $this->symbol->valueOf() === $object->entity->symbol()->valueOf(),
        );
    }

    public function toInt(): self
    {
        return clone $this;
    }

    public function toFloat(): Float_
    {
        return Float_::createBy(
            (float) $this->symbol->valueOf(),
        );
    }

    public function toString(): String_
    {
        return String_::createBy(
            (string) $this,
        );
    }

    public static function createBy(mixed $value = 0): self
    {
        return new self(new NumberSymbol($value));
    }
}
