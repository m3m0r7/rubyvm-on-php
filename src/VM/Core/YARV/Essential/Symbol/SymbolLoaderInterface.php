<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

interface SymbolLoaderInterface
{
    public function load(): SymbolInterface;
}
