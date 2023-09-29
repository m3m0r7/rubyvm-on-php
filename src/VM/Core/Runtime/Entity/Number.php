<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
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

    #[BindAliasAs('^')]
    public function xor(CallInfoInterface $callInfo, RubyClassInterface $object): Number
    {
        return Number::createBy(
            $this->symbol->valueOf() ^ $object->entity()->symbol()->valueOf(),
        );
    }

    #[BindAliasAs('**')]
    public function power(CallInfoInterface $callInfo, RubyClassInterface $object): Number
    {
        return Number::createBy(
            $this->symbol->valueOf() ** $object->entity()->symbol()->valueOf(),
        );
    }

    #[BindAliasAs('>>')]
    public function rightShift(CallInfoInterface $callInfo, RubyClassInterface $object): Number
    {
        return Number::createBy(
            $this->symbol->valueOf() >> $object->entity()->symbol()->valueOf(),
        );
    }

    #[BindAliasAs('===')]
    public function compareStrictEquals(CallInfoInterface $callInfo, RubyClassInterface $object): Boolean_
    {
        return Boolean_::createBy(
            $this->symbol->valueOf() === $object->entity()->symbol()->valueOf(),
        );
    }

    #[BindAliasAs('to_i')]
    public function toInt(): self
    {
        return clone $this;
    }

    #[BindAliasAs('to_f')]
    public function toFloat(): Float_
    {
        return Float_::createBy(
            (float) $this->symbol->valueOf(),
        );
    }

    #[BindAliasAs('to_s')]
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
