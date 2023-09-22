<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\DefinedClassEntries;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

interface RubyClassInterface extends RubyClassImplementationInterface
{
    public function setInstanceVariable(ID $id, Object_ $object): void;

    public function getInstanceVariable(ID $id): Object_;

    public function classes(): array;

    public function methods(): array;

    public function hasMethod(string $name): bool;

    public function class(NumberSymbol $flags, StringSymbol $className): void;

    public function def(StringSymbol $methodName, ContextInterface $context): void;
}
