<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\Entity\Entity;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;

#[BindAliasAs('Boolean')]
class Boolean_ extends Object_ implements RubyClassInterface
{
    public function __construct(private BooleanSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    #[BindAliasAs('to_s')]
    public function toString(): String_
    {
        return String_::createBy(
            (string) $this,
        );
    }

    public function testValue(): bool
    {
        return $this->symbol->valueOf();
    }

    public static function createBy(mixed $value = true): self
    {
        return new self(new BooleanSymbol($value));
    }
}
