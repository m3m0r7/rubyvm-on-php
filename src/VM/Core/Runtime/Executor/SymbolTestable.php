<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

trait SymbolTestable
{
    private function test(SymbolInterface $symbol): bool
    {
        return (bool) (match ($symbol::class) {
            BooleanSymbol::class,
            NumberSymbol::class,
            StringSymbol::class => $symbol->valueOf(),
            default => throw new OperationProcessorException(sprintf('The symbol type `%s` is not implemented `test` processing yet', ClassHelper::nameBy($symbol))),
        });
    }

    private function unless(SymbolInterface $symbol): bool
    {
        return !$this->test($symbol);
    }
}
