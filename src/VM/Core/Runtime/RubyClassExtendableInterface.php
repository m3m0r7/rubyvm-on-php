<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

interface RubyClassExtendableInterface
{
    public function extendClassEntry(ExtendedClassEntry $extendedClassEntry): self;
    public function class(NumberSymbol $flags, StringSymbol $className, ContextInterface $context): void;
    public function def(StringSymbol $methodName, ContextInterface $context): void;
    public function getDefinedClassOrSelf(RubyClassImplementationInterface|Object_ $class): RubyClassImplementationInterface|Object_|SymbolInterface;
    public function classes(): array;

    public function methods(): array;

    public function hasMethod(string $name): bool;
}
