<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolize;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;

#[BindAliasAs('TrueClass')]
class TrueClass extends Object_ implements RubyClassInterface, Symbolize
{
    use Symbolizable;

    public function __construct()
    {
        $this->symbol = new BooleanSymbol(true);
    }

    public static function createBy(): self
    {
        static $cache;

        return $cache ??= new self();
    }

    #[BindAliasAs('to_s')]
    public function toString(): String_
    {
        return String_::createBy('true');
    }

    public function testValue(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return (string) $this->symbol;
    }
}
