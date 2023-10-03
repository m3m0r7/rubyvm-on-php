<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\SymbolizeInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;

#[BindAliasAs('Float')]
class Float_ extends Comparable implements RubyClassInterface, SymbolizeInterface
{
    use Symbolizable;

    final public const INFINITY = INF;

    public function __construct(FloatSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = 0): self
    {
        return new self(new FloatSymbol($value));
    }

    #[BindAliasAs('+')]
    public function plus(RubyClassInterface $object): Float_
    {
        return Float_::createBy(
            $this->valueOf() + $object->valueOf(),
        );
    }

    #[BindAliasAs('-')]
    public function minus(RubyClassInterface $object): Float_
    {
        return Float_::createBy(
            $this->valueOf() - $object->valueOf(),
        );
    }

    #[BindAliasAs('*')]
    public function multiply(RubyClassInterface $object): Float_
    {
        return Float_::createBy(
            $this->valueOf() * $object->valueOf(),
        );
    }

    #[BindAliasAs('/')]
    public function divide(RubyClassInterface $object): Float_
    {
        return Float_::createBy(
            $this->valueOf() / $object->valueOf(),
        );
    }
}
