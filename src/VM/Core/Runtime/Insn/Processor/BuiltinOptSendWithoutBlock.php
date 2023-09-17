<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethodCallerEntries;
use RubyVM\VM\Core\Runtime\Executor\Translatable;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\RubyClassExtendableInterface;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\SpecialMethod\SpecialMethodInterface;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinOptSendWithoutBlock implements OperationProcessorInterface
{
    use OperandHelper;
    use Validatable;
    use Translatable;

    private Insn $insn;

    private ContextInterface $context;
    private static SpecialMethodCallerEntries $specialMethodCallerEntries;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;

        static::$specialMethodCallerEntries ??= new SpecialMethodCallerEntries();
    }

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    public function process(): ProcessedStatus
    {
        $callInfo = $this->getOperandAsCallInfo();

        /**
         * @var StringSymbol $symbol
         */
        $symbol = $callInfo
            ->callData()
            ->mid()
            ->object
            ->symbol
        ;

        $arguments = [];
        for ($i = 0; $i < $callInfo->callData()->argumentsCount(); ++$i) {
            $arguments[] = $operand = $this->context->vmStack()->pop();
        }

        $this->validateType(
            OperandEntry::class,
            ...$arguments,
        );

        /**
         * @var Object_|RubyClassInterface $targetSymbol
         */
        $targetSymbol = $this->getStack()
            ->operand
        ;

        $targetClass = $this->context
            ->self()
            ->getDefinedClassOrSelf($targetSymbol)
        ;

        $result = null;

        // TODO: will refactor here
        if ($targetClass instanceof RubyClassInterface && $targetClass->hasMethod('injectVMContext')) {
            $targetClass->injectVMContext($this->context->kernel());
        }

        // Here is a special method calls
        $lookupSpecialMethodName = (string) $symbol;
        if (static::$specialMethodCallerEntries->has($lookupSpecialMethodName)) {
            if (!$targetClass instanceof RubyClassExtendableInterface) {
                throw new OperationProcessorException('The callee class is invalid (not instantiated by the RubyClassExtendableInterface)');
            }

            /**
             * @var SpecialMethodInterface $calleeSpecialMethodName
             */
            $calleeSpecialMethodName = static::$specialMethodCallerEntries
                ->get($lookupSpecialMethodName)
            ;

            $result = $calleeSpecialMethodName->process(
                $targetClass,
                ...$this->translateForArguments(...$arguments),
            );
        } else {
            /**
             * @var null|ExecutedResult|SymbolInterface $result
             */
            $result = $targetClass
                ->{(string) $symbol}(...$this->translateForArguments(...$arguments))
            ;
        }

        if ($result instanceof Object_) {
            $this->context->vmStack()
                ->push(new OperandEntry($result))
            ;

            return ProcessedStatus::SUCCESS;
        }

        if (null === $result) {
            // This is same at UNDEFINED on originally RubyVM
            return ProcessedStatus::SUCCESS;
        }

        if ($result instanceof ExecutedResult) {
            if ($result->threw) {
                throw $result->threw;
            }
            if (null !== $result->returnValue) {
                $this->context->vmStack()
                    ->push(new OperandEntry($result->returnValue))
                ;
            }

            return ProcessedStatus::SUCCESS;
        }
        if ($result instanceof SymbolInterface) {
            $this->context->vmStack()
                ->push(new OperandEntry($result->toObject()))
            ;

            return ProcessedStatus::SUCCESS;
        }

        throw new OperationProcessorException('Unreachable here because the opt_send_without_block is not properly implementation');
    }
}
