<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Helper\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Executor\ClassResolver;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethod\ExtendedClassEntry;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

trait RubyClassExtendable
{
    protected array $userLandClasses = [];
    protected array $userLandMethods = [];

    public function class(NumberSymbol $flags, StringSymbol $className, ContextInterface $context): void
    {
        if (!isset($this->userLandClasses[(string) $className])) {
            $this->userLandClasses[(string) $className] = new ExtendedClassEntry();
        }
        $executor = new Executor(
            kernel: $context->kernel(),
            classImplementation: $this->userLandClasses[(string) $className],
            instructionSequence: $context->instructionSequence(),
            logger: $context->logger(),
            debugger: $context->debugger(),
            previousContext: $context,
        );

        $result = $executor->execute();

        if ($result->throwed) {
            throw $result->throwed;
        }
    }

    public function def(StringSymbol $methodName, ContextInterface $context): void
    {
        $this->userLandMethods[(string) $methodName] = $context;
    }

    public function getDefinedClass($className)
    {
        var_dump($className);
        exit();
    }

    public function classes(): array
    {
        return array_keys($this->userLandClasses);
    }

    public function methods(): array
    {
        return array_keys($this->userLandMethods);
    }

    public function __call(string $name, array $arguments): ExecutedResult
    {
        /**
         * @var null|ContextInterface $context
         */
        $context = $this->userLandMethods[$name] ?? null;

        if (null === $context) {
            throw new OperationProcessorException(sprintf('Method not found %s#%s', ClassHelper::nameBy($this), $name));
        }

        $executor = (new Executor(
            kernel: $context->kernel(),
            classImplementation: $context->self(),
            instructionSequence: $context->instructionSequence(),
            logger: $context->logger(),
            debugger: $context->debugger(),
            previousContext: $context
                ->renewEnvironmentTableEntries(),
        ));

        $envIndex = LocalTableHelper::calculateFirstLocalTableIndex(
            $context,
            $arguments,
        );

        /**
         * @var SymbolInterface $argument
         */
        foreach ($arguments as $index => $argument) {
            $executor->context()->environmentTableEntries()
                ->get(Option::RSV_TABLE_INDEX_0)
                ->set(
                    $envIndex + $index,
                    $argument->toObject(),
                )
            ;
        }

        return $executor->execute();
    }
}
