<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

interface SymbolInterface
{
    public function __toString(): string;
}