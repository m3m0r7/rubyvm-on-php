<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\ExtendedClassEntry;
use RubyVM\VM\Core\Runtime\RubyClassImplementationInterface;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

trait ProvideClassExtendableMethods
{
    public function getDefinedClassOrSelf(RubyClassImplementationInterface|Object_ $class): RubyClassImplementationInterface|Object_|SymbolInterface
    {
        if ($class instanceof RubyClassImplementationInterface) {
            return $class;
        }

        /**
         * @var ExtendedClassEntry $userLandClass
         */
        foreach (static::$userLandClasses as $userLandClass) {
            if ($userLandClass->isBound(get_class($class->symbol))) {
                return $class->symbol->extendClassEntry($userLandClass);
            }
        }

        return $class;
    }

    public function classes(): array
    {
        return array_keys(static::$userLandClasses);
    }

    public function methods(): array
    {
        return [
            ...array_map(
                fn (\ReflectionMethod $method) => $method->name,
                (new \ReflectionClass($this))
                    ->getMethods(),
            ),
            ...array_keys(static::$userLandMethods),
        ];
    }

    public function hasMethod(string $name): bool
    {
        return in_array($name, $this->methods(), true) || ($this->extendedClassEntry && $this->extendedClassEntry->hasMethod($name));
    }

    public function class(NumberSymbol $flags, StringSymbol $className, ContextInterface $context): void
    {
        if (!isset(static::$userLandClasses[(string) $className])) {
            static::$userLandClasses[(string) $className] = new ExtendedClassEntry((string) $className);
        }
        $executor = new Executor(
            kernel: $context->kernel(),
            classImplementation: static::$userLandClasses[(string) $className],
            instructionSequence: $context->instructionSequence(),
            logger: $context->logger(),
            debugger: $context->debugger(),
            previousContext: $context,
        );

        $result = $executor->execute();

        if ($result->threw) {
            throw $result->threw;
        }
    }

    public function def(StringSymbol $methodName, ContextInterface $context): void
    {
        static::$userLandMethods[(string) $methodName] = $context;
    }
}
