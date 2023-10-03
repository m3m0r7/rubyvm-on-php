<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\FalseClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\TrueClass;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\SymbolizeInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;

class Integer_ extends Comparable implements RubyClassInterface, SymbolizeInterface
{
    use Symbolizable;

    public function __construct(NumberSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function testValue(): bool
    {
        return (bool) $this->valueOf();
    }

    #[BindAliasAs('^')]
    public function xor(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() ^ $object->valueOf(),
        );
    }

    #[BindAliasAs('**')]
    public function power(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() ** $object->valueOf(),
        );
    }

    #[BindAliasAs('>>')]
    public function rightShift(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() >> $object->valueOf(),
        );
    }

    #[BindAliasAs('<<')]
    public function leftShift(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() << $object->valueOf(),
        );
    }

    #[BindAliasAs('===')]
    public function compareStrictEquals(CallInfoInterface $callInfo, RubyClassInterface $object): TrueClass|FalseClass
    {
        return $this->valueOf() === $object->valueOf()
            ? TrueClass::createBy()
            : FalseClass::createBy();
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
            (float) $this->valueOf(),
        );
    }

    #[BindAliasAs('to_s')]
    public function toString(): String_
    {
        return String_::createBy(
            (string) $this,
        );
    }

    #[BindAliasAs('+')]
    public function plus(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() + $object->valueOf(),
        );
    }

    #[BindAliasAs('-')]
    public function minus(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() - $object->valueOf(),
        );
    }

    #[BindAliasAs('*')]
    public function multiply(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() * $object->valueOf(),
        );
    }

    #[BindAliasAs('/')]
    public function divide(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            (int) ($this->valueOf() / $object->valueOf()),
        );
    }

    #[BindAliasAs('&')]
    public function and(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() & $object->valueOf(),
        );
    }

    #[BindAliasAs('|')]
    public function or(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() | $object->valueOf(),
        );
    }

    #[BindAliasAs('%')]
    public function mod(CallInfoInterface $callInfo, RubyClassInterface $object): Integer_
    {
        return Integer_::createBy(
            $this->valueOf() % $object->valueOf(),
        );
    }

    #[BindAliasAs('==')]
    public function equals(CallInfoInterface $callInfo, RubyClassInterface $object): TrueClass|FalseClass
    {
        return $this->valueOf() == $object->valueOf()
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    #[BindAliasAs('<=')]
    public function equalsOrLessThan(CallInfoInterface $callInfo, RubyClassInterface $object): TrueClass|FalseClass
    {
        return $this->valueOf() <= $object->valueOf()
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    #[BindAliasAs('>=')]
    public function equalsOrGreaterThan(CallInfoInterface $callInfo, RubyClassInterface $object): TrueClass|FalseClass
    {
        return $this->valueOf() >= $object->valueOf()
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    #[BindAliasAs('<')]
    public function lessThan(CallInfoInterface $callInfo, RubyClassInterface $object): TrueClass|FalseClass
    {
        return $this->valueOf() < $object->valueOf()
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    #[BindAliasAs('>')]
    public function greaterThan(CallInfoInterface $callInfo, RubyClassInterface $object): TrueClass|FalseClass
    {
        return $this->valueOf() > $object->valueOf()
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    #[BindAliasAs('even?')]
    public function isEven(CallInfoInterface $callInfo): TrueClass|FalseClass
    {
        return ($this->valueOf() & 1) === 0
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    #[BindAliasAs('odd?')]
    public function isOdd(CallInfoInterface $callInfo): TrueClass|FalseClass
    {
        return ($this->valueOf() & 1) === 1
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    #[BindAliasAs('zero?')]
    public function isZero(CallInfoInterface $callInfo): TrueClass|FalseClass
    {
        return $this->valueOf() === 0
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    public static function createBy(mixed $value = 0): self
    {
        return new self(new NumberSymbol($value));
    }
}
