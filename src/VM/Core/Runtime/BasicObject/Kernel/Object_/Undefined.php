<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\SymbolizeInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\UndefinedSymbol;

class Undefined extends Object_ implements RubyClassInterface, SymbolizeInterface
{
    use Symbolizable;

    public function __construct(UndefinedSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = null): self
    {
        return new self(new UndefinedSymbol());
    }
}
