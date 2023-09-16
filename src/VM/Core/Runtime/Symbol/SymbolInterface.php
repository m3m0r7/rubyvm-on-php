<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\RubyClassInterface;

interface SymbolInterface
{
    public function toObject(): Object_;

    public function __toString(): string;
}
