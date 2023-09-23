<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\YARV\Criterion\ObjectifyInterface;

interface SymbolInterface extends ObjectifyInterface
{
    public function valueOf(): mixed;

    public function __toString(): string;
}
