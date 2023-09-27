<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
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

    public function xor(CallInfoInterface $callInfo, RubyClassInterface $object): Number
    {
        return Number::createBy(
            $this->symbol->valueOf() ^ $object->entity()->symbol()->valueOf(),
        );
    }

    public function power(CallInfoInterface $callInfo, RubyClassInterface $object): Number
    {
        return Number::createBy(
            $this->symbol->valueOf() ** $object->entity()->symbol()->valueOf(),
        );
    }

    public function rightShift(CallInfoInterface $callInfo, RubyClassInterface $object): Number
    {
        return Number::createBy(
            $this->symbol->valueOf() >> $object->entity()->symbol()->valueOf(),
        );
    }

    public function compareStrictEquals(CallInfoInterface $callInfo, RubyClassInterface $object): Boolean_
    {
        return Boolean_::createBy(
            $this->symbol->valueOf() === $object->entity()->symbol()->valueOf(),
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
