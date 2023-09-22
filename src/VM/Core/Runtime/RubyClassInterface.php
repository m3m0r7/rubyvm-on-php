<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;

interface RubyClassInterface extends RubyClassImplementationInterface
{
    public function setRuntimeContext(?ContextInterface $context): RubyClassInterface;

    public function userlandHeapSpace(): ?UserlandHeapSpace;

    public function setUserlandHeapSpace(?UserlandHeapSpace $userlandHeapSpace): RubyClassInterface;

    public function classes(): array;

    public function methods(): array;

    public function hasMethod(string $name): bool;

    public function class(NumberSymbol $flags, StringSymbol $className): void;

    public function def(StringSymbol $methodName, ContextInterface $context): void;
}
