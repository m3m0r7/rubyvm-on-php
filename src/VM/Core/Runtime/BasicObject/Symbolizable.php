<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject;

use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

trait Symbolizable
{
    protected SymbolInterface $symbol;

    public function __clone()
    {
        // Deep copy bound symbol
        $this->symbol = clone $this->symbol;
    }

    public function __toString()
    {
        return (string) $this->symbol;
    }

    public function symbol(): SymbolInterface
    {
        return $this->symbol;
    }

    public function valueOf(): mixed
    {
        return $this->symbol->valueOf();
    }
}
