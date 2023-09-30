<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Entity\Entity;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;

class NilClass extends Object_ implements RubyClassInterface
{
    public function __construct(private NilSymbol $symbol)
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
        static $symbol = new NilSymbol();

        return new self($symbol);
    }
}
