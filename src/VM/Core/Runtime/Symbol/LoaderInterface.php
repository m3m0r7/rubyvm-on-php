<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

interface LoaderInterface
{
    public function load(): SymbolInterface;
}
