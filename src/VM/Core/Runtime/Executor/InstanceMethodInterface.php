<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

interface InstanceMethodInterface
{
    public static function name(): string;
    public function process(SymbolInterface $symbol, ...$arguments): SymbolInterface;
}
