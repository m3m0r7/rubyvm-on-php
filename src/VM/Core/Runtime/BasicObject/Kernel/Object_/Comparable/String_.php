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
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

#[BindAliasAs('String')]
class String_ extends Comparable implements RubyClassInterface, SymbolizeInterface
{
    use Symbolizable;

    public function __construct(StringSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function testValue(): bool
    {
        return (bool) $this->symbol->valueOf();
    }

    public static function createBy(mixed $value = ''): self
    {
        return new self(new StringSymbol($value));
    }

    #[BindAliasAs('+')]
    public function plus(CallInfoInterface $callInfo, RubyClassInterface $object): String_
    {
        return String_::createBy(
            $this->valueOf() . $object->valueOf(),
        );
    }

    #[BindAliasAs('empty?')]
    public function isEmpty(CallInfoInterface $callInfo): TrueClass|FalseClass
    {
        return $this->valueOf() === ''
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    #[BindAliasAs('include?')]
    public function isIncluding(CallInfoInterface $callInfo, String_ $string): TrueClass|FalseClass
    {
        return str_contains((string) $this->valueOf(), (string) $string->valueOf())
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }
}
