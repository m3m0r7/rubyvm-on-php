<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\SpecialMethodCallerEntries;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Exception\RuntimeException;

trait ProvideClassExtendableMethods
{
    protected ?UserlandHeapSpaceInterface $userlandHeapSpace = null;

    public function userlandHeapSpace(): UserlandHeapSpaceInterface
    {
        if ($this->userlandHeapSpace === null) {
            throw new RuntimeException(
                'The userland heapspace is null',
            );
        }

        return $this->userlandHeapSpace;
    }

    public function setUserlandHeapSpace(?UserlandHeapSpaceInterface $userlandHeapSpace): self
    {
        $this->userlandHeapSpace = $userlandHeapSpace;

        return $this;
    }

    /**
     * @return string[]
     */
    public function classes(): array
    {
        return array_keys($this->userlandHeapSpace()->userlandClasses()->toArray());
    }

    /**
     * @return string[]
     */
    public function methods(): array
    {
        return [
            ...array_map(
                static fn (\ReflectionMethod $method) => $method->name,
                (new \ReflectionClass($this))
                    ->getMethods(),
            ),
            ...array_keys(SpecialMethodCallerEntries::map()),
            ...array_keys($this->userlandHeapSpace()->userlandMethods()->toArray()),
        ];
    }

    public function hasMethod(string $name): bool
    {
        return in_array($name, $this->methods(), true);
    }

    public function class(NumberSymbol $flags, StringSymbol $className): void
    {
        $className = (string) $className;

        $this->userlandHeapSpace()
            ->userlandClasses()
            ->set(
                $className,
                $this->userlandHeapSpace()
                    ->userlandClasses()
                    ->get($className) ?? new UserlandHeapSpace(),
            );
    }

    public function def(StringSymbol $methodName, ContextInterface $context): void
    {
        $context->self()
            ->userlandHeapSpace()
            ->userlandMethods()
            ->set((string) $methodName, $context);
    }
}
