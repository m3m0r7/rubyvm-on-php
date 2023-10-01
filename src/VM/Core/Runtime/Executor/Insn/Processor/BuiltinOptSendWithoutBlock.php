<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\SpecialMethodCallerEntries;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethod\SpecialMethodInterface;
use RubyVM\VM\Core\Runtime\Executor\ArgumentTransformable;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinOptSendWithoutBlock implements OperationProcessorInterface
{
    use OperandHelper;
    use Validatable;
    use ArgumentTransformable;

    private Insn $insn;

    private ContextInterface $context;

    private static SpecialMethodCallerEntries $specialMethodCallerEntries;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;

        self::$specialMethodCallerEntries ??= new SpecialMethodCallerEntries();
    }

    public function before(): void {}

    public function after(): void {}

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        $callInfo = $this->operandAsCallInfo();

        /**
         * @var StringSymbol $symbol
         */
        $symbol = $callInfo
            ->callData()
            ->mid()
            ->object;

        $arguments = [];
        for ($i = 0; $i < $callInfo->callData()->argumentsCount(); ++$i) {
            $arguments[] = $operand = $this->context->vmStack()->pop();
        }

        $this->validateType(
            Operand::class,
            ...$arguments,
        );

        $targetClass = $targetObjectOrClass = $this->getStack()
            ->operand;

        if (!$targetClass instanceof RubyClassInterface) {
            throw new OperationProcessorException(
                sprintf(
                    'Unexpected receiver class: %s',
                    ClassHelper::nameBy($targetClass),
                ),
            );
        }

        $targetClass->setRuntimeContext($this->context);

        $result = null;

        // Here is a special method calls
        $lookupSpecialMethodName = (string) $symbol;
        if (self::$specialMethodCallerEntries->has($lookupSpecialMethodName)) {
            /**
             * @var SpecialMethodInterface $calleeSpecialMethodName
             */
            $calleeSpecialMethodName = self::$specialMethodCallerEntries
                ->get($lookupSpecialMethodName);

            $result = $calleeSpecialMethodName->process(
                $targetClass,
                $this->context,
                $callInfo,
                ...$this->translateForArguments(...$arguments),
            );
        } else {
            /**
             * @var null|ExecutedResult|RubyClassInterface $result
             */
            $result = $targetClass
                ->{(string) $symbol}(
                    $callInfo,
                    ...$this->translateForArguments(...$arguments),
                );
        }

        if ($result instanceof RubyClassInterface) {
            $this->context->vmStack()
                ->push(new Operand($result));

            return ProcessedStatus::SUCCESS;
        }

        if ($result === null) {
            // This is same at UNDEFINED on originally RubyVM
            return ProcessedStatus::SUCCESS;
        }

        if ($result instanceof ExecutedResult) {
            if ($result->threw instanceof \Throwable) {
                throw $result->threw;
            }

            if ($result->returnValue instanceof \RubyVM\VM\Core\Runtime\Essential\RubyClassInterface) {
                $this->context->vmStack()
                    ->push(new Operand($result->returnValue));
            }

            return ProcessedStatus::SUCCESS;
        }

        throw new OperationProcessorException('Unreachable here because the opt_send_without_block is not properly implementation');
    }
}
