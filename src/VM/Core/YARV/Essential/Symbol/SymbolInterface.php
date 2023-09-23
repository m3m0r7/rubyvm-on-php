<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\YARV\Essential\RubyClassifiable;

interface SymbolInterface extends RubyClassifiable
{
    public function valueOf(): mixed;

    public function __toString(): string;
}
