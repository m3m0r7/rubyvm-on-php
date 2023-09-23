<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Essential\Symbol;

use RubyVM\VM\Core\Runtime\Object_;

class ClassSymbol implements SymbolInterface
{
    public function __construct(
        private StringSymbol $class,
    ) {}

    public function valueOf(): string
    {
        return $this->class->valueOf();
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::CLASS_,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: clone $this,
        );
    }

    public function __toString(): string
    {
        return $this->valueOf();
    }
}
