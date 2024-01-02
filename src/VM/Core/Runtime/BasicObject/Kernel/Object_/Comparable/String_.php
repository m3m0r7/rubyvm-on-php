<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Array_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\FalseClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\TrueClass;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\SymbolizeInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
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

    public function chars(): Array_
    {
        $array = [];
        $string = (string) $this->symbol;
        for ($i = 0; $i < strlen($string); ++$i) {
            $array[] = String_::createBy($string[$i]);
        }

        return Array_::createBy($array);
    }

    #[BindAliasAs('+')]
    public function plus(RubyClassInterface $object): String_
    {
        return String_::createBy(
            $this->valueOf() . $object->valueOf(),
        );
    }

    #[BindAliasAs('empty?')]
    public function isEmpty(): FalseClass|TrueClass
    {
        return $this->valueOf() === ''
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    #[BindAliasAs('include?')]
    public function isIncluding(String_ $string): FalseClass|TrueClass
    {
        return str_contains((string) $this->valueOf(), (string) $string->valueOf())
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }

    public function inspect(): RubyClassInterface
    {
        return String_::createBy(sprintf(
            '"%s"',
            (string) $this,
        ));
    }
}
