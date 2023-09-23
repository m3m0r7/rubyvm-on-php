<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Essential\Symbol;

interface LoaderInterface
{
    public function load(): SymbolInterface;
}
