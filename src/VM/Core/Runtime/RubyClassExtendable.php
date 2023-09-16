<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Helper\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;
use RubyVM\VM\Exception\RubyVMException;

trait RubyClassExtendable
{
    protected static array $userLandClasses = [];
    protected static array $userLandMethods = [];

    protected ?ExtendedClassEntry $extendedClassEntry = null;

    public function extendClassEntry(ExtendedClassEntry $extendedClassEntry): self
    {
        $this->extendedClassEntry = $extendedClassEntry;

        return $this;
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
        return array_keys(static::$userLandMethods);
    }

    public function hasMethod(string $name): bool
    {
        return in_array($name, $this->methods(), true) || ($this->extendedClassEntry && $this->extendedClassEntry->hasMethod($name));
    }

    public function __call(string $name, array $arguments): ExecutedResult|SymbolInterface
    {
        if ($this->extendedClassEntry && $this->extendedClassEntry->hasMethod($name)) {
            return $this->extendedClassEntry->{$name}(...$arguments);
        }

        /**
         * @var null|ContextInterface $context
         */
        $context = static::$userLandMethods[$name] ?? null;

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

        $localTableSize = $executor->context()->instructionSequence()->body()->data->localTableSize();

        for ($localIndex = 0, $i = count($arguments) - 1; $i >= 0; $i--, $localIndex++) {
            /**
             * @var SymbolInterface $argument
             */
            $argument = $arguments[$i];
            $executor->context()
                ->environmentTableEntries()
                ->get(0)
                ->set(
                    LocalTableHelper::computeLocalTableIndex(
                        $localTableSize,
                        Option::VM_ENV_DATA_SIZE + $localTableSize - $localIndex - 1,
                    ),
                    $argument->toObject(),
                )
            ;
        }

        $executed = $executor->execute();

        if ($executed->executedStatus !== ExecutedStatus::SUCCESS) {
            if (ExecutedStatus::THREW_EXCEPTION) {
                throw $executed->threw;
            }
            throw new RubyVMException('An exception occurred by some reason then RubyVM executor returned incorrect status');
        }

        return $executed;
    }
}
