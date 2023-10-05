<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\SymbolizeInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;

#[BindAliasAs('FalseClass')]
class FalseClass extends Object_ implements RubyClassInterface, SymbolizeInterface
{
    use Symbolizable;

    public function __construct()
    {
        $this->symbol = new BooleanSymbol(false);
    }

    public static function createBy(): self
    {
        static $cache;

        return $cache ??= new self();
    }

    #[BindAliasAs('to_s')]
    public function toString(): String_
    {
        return String_::createBy('false');
    }

    public function testValue(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return (string) $this->symbol;
    }

    public function inspect(): RubyClassInterface
    {
        return String_::createBy((string) $this);
    }
}
