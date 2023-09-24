<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

interface SymbolInterface
{
    public function valueOf(): mixed;

    public function __toString(): string;
}
