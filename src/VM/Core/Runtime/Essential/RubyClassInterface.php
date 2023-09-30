<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Class_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolSymbol;

interface RubyClassInterface extends RubyClassImplementationInterface, \Stringable
{
    public function setRuntimeContext(?ContextInterface $context): RubyClassInterface;

    public function context(): ContextInterface;

    public function userlandHeapSpace(): UserlandHeapSpaceInterface;

    public function setUserlandHeapSpace(?UserlandHeapSpaceInterface $userlandHeapSpace): self;

    /**
     * @return string[]
     */
    public function classes(): array;

    /**
     * @return string[]
     */
    public function methods(): array;

    public function hasMethod(string $name): bool;

    public function class(NumberSymbol $flags, StringSymbol|SymbolSymbol $className): void;

    public function def(StringSymbol|SymbolSymbol $methodName, ContextInterface $context): void;
}
