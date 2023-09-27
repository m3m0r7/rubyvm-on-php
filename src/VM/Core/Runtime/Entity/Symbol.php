<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolSymbol;

class Symbol extends Entity implements EntityInterface
{
    public function __construct(SymbolSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function testValue(): bool
    {
        return (bool) $this->symbol->valueOf();
    }

    public static function createBy(mixed $value = ''): self
    {
        return new self(new SymbolSymbol($value));
    }
}
