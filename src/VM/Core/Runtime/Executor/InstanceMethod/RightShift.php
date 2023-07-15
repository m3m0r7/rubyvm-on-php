<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\InstanceMethod;

use RubyVM\VM\Core\Runtime\Executor\InstanceMethodInterface;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

class RightShift implements InstanceMethodInterface
{
    public static function name(): string
    {
        return '>>';
    }

    public function process(SymbolInterface $symbol, ...$arguments): SymbolInterface
    {
        return $symbol->rightShift(...$arguments);
    }
}
