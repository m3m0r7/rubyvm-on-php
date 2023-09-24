<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Essential\EntityInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class Boolean extends Entity implements EntityInterface
{
    public function __construct(BooleanSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function toString(): String_
    {
        return new String_(new StringSymbol(
            string: (string) $this,
        ));
    }

    public function testValue(): bool
    {
        return $this->symbol->valueOf();
    }
}