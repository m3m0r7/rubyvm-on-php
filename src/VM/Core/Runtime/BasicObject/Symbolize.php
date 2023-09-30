<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject;

use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

interface Symbolize
{
    public function symbol(): SymbolInterface;
}
