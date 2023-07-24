<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\InstanceMethod;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Helper\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethodInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\ClassNotFoundException;

class ClassExtender
{
    protected array $extendedMethodsInClasses = [];

    public function __construct()
    {

    }

    public function call(string $class, string $name, array $arguments): ExecutedResult
    {
        /**
         * @var ContextInterface|null $context
         */
        $context = $this->extendedClasses[$class][$name] ?? null;

        if ($context === null) {
            throw new ClassNotFoundException(
                sprintf(
                    'Not found extendable class (%s)',
                    $class,
                ),
            );
        }

        $executor = (new Executor(
            kernel: $context->kernel(),
            main: $context->self(),
            instructionSequence: $context->instructionSequence(),
            logger: $context->logger(),
            debugger: $context->debugger(),
            previousContext: $context,
        ));

        $envIndex = LocalTableHelper::calculateFirstLocalTableIndex(
            $context,
            $arguments,
        );

        foreach ($arguments as $index => $argument) {
            $executor->context()
                ->environmentTableEntries()
                ->get(Option::RSV_TABLE_INDEX_0)
                ->set(
                    $envIndex + $index,
                    $argument->toObject(),
                );
        }

        $executor->context()
            ->appendTrace($class . '#' . $name)
        ;

        $result = $executor->execute();

        // An occurred exception to be throwing
        if ($result->throwed) {
            throw $result->throwed;
        }

        return $result;
    }

    public function is(string $class): bool
    {
        return isset($this->extendedClasses[$class]);
    }

    public function extendMethod(string $class, string $name, ContextInterface $context): self
    {
        if (!class_exists($class)) {
            throw new ClassNotFoundException(
                sprintf(
                    'Not found extendable class (%s)',
                    $class,
                ),
            );
        }
        if (!isset($this->extendedClasses[$class])) {
            $this->extendedMethodsInClasses[$class] = [];
        }
        $this->extendedMethodsInClasses[$class][$name] = $context;
        return $this;
    }
}
