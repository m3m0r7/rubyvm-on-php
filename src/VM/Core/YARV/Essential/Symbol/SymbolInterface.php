<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Runtime\Essential\RubyClassifiable;

interface SymbolInterface
{
    public function valueOf(): mixed;

    public function __toString(): string;
}
