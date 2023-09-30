<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Symbol;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;

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

    public function class(NumberSymbol $flags, String_|Symbol $className): void;

    public function def(String_|Symbol $methodName, ContextInterface $context): void;

    public function valueOf(): mixed;
}
