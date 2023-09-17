<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\DefinedClassEntries;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

interface RubyClassImplementationInterface
{
    public function def(StringSymbol $methodName, ContextInterface $context): void;

    public function class(NumberSymbol $flags, StringSymbol $className, ContextInterface $context): void;

    public function __call(string $name, array $arguments): ExecutedResult|SymbolInterface;

    public function puts(SymbolInterface $symbol): SymbolInterface;

    public function exit(int $code = 0): void;

    public function injectVMContext(
        KernelInterface $kernel,
        DefinedClassEntries $definedClassEntries = null,
    ): self;
}
