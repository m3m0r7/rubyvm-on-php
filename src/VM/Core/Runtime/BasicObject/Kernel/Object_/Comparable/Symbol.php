<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable;

use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\SymbolizeInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolSymbol;

class Symbol extends Comparable implements RubyClassInterface, SymbolizeInterface
{
    use Symbolizable;

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

    public function inspect(): RubyClassInterface
    {
        return String_::createBy((string) $this);
    }
}
