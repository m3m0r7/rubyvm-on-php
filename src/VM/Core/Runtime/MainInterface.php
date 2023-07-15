<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

interface MainInterface
{
    public function puts(SymbolInterface $symbol): void;
}
