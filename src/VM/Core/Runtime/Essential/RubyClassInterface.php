<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

interface RubyClassInterface extends RubyClassImplementationInterface, \Stringable
{
    public function entity(): EntityInterface;

    public function setRuntimeContext(?ContextInterface $context): RubyClassInterface;

    public function userlandHeapSpace(): ?UserlandHeapSpaceInterface;

    public function setUserlandHeapSpace(?UserlandHeapSpaceInterface $userlandHeapSpace): RubyClassInterface;

    /**
     * @return string[]
     */
    public function classes(): array;

    /**
     * @return string[]
     */
    public function methods(): array;

    public function hasMethod(string $name): bool;

    public function class(NumberSymbol $flags, StringSymbol $className): void;

    public function def(StringSymbol $methodName, ContextInterface $context): void;
}
