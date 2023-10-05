<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;

class NilClass extends Object_ implements RubyClassInterface
{
    use Symbolizable;

    public function __construct(NilSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function testValue(): bool
    {
        // Always return false
        return false;
    }

    public static function createBy(mixed $value = null): self
    {
        static $cache;

        return $cache ??= new self(new NilSymbol());
    }

    public function inspect(): RubyClassInterface
    {
        return String_::createBy((string) $this);
    }
}
