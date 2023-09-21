<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;

trait ProvideClassExtendableMethods
{
    protected ?UserlandHeapSpace $userlandHeapSpace = null;

    public function userlandHeapSpace(): UserlandHeapSpace
    {
        return $this->userlandHeapSpace;
    }

    public function tryToSetUserlandHeapSpace(UserlandHeapSpace $userlandHeapSpace): self
    {
        $this->userlandHeapSpace ??= $userlandHeapSpace;
        return $this;
    }

    public function classes(): array
    {
        return array_keys($this->userlandHeapSpace->userlandClasses()->toArray());
    }

    public function methods(): array
    {
        return [
            ...array_map(
                fn (\ReflectionMethod $method) => $method->name,
                (new \ReflectionClass($this))
                    ->getMethods(),
            ),
            ...array_keys($this->userlandHeapSpace->userlandMethods()->toArray())
        ];
    }

    public function hasMethod(string $name): bool
    {
        return in_array($name, $this->methods(), true);
    }

    public function class(NumberSymbol $flags, StringSymbol $className, Executor $executor): void
    {
        $this->userlandHeapSpace->userlandClasses()->set((string) $className, $executor->context());

        $result = $executor->execute();

        if ($result->threw) {
            throw $result->threw;
        }
    }

    public function def(StringSymbol $methodName, ContextInterface $context): void
    {
        $this->userlandHeapSpace
            ->userlandMethods()
            ->set((string) $methodName, $context);
    }
}
