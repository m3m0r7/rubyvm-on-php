<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Helper\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard\Main;
use RubyVM\VM\Exception\OperationProcessorException;
use RubyVM\VM\Exception\RubyVMException;

trait ProvideExtendedMethodCall
{
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
            rubyClass: $context->self(),
            instructionSequence: $context->instructionSequence(),
            logger: $context->logger(),
            debugger: $context->debugger(),
            previousContext: $context
                ->renewEnvironmentTable(),
        ));

        // TODO: is this needed?
        if (!$context->self() instanceof Main) {
            $executor->context()->vmStack()->push(
                new OperandEntry(
                    $context->self(),
                ),
            );
        }

        $localTableSize = $executor->context()->instructionSequence()->body()->data->localTableSize();

        for ($localIndex = 0, $i = count($arguments) - 1; $i >= 0; $i--, $localIndex++) {
            /**
             * @var SymbolInterface $argument
             */
            $argument = $arguments[$i];
            $executor->context()
                ->environmentTable()
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
