<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\SpecialMethodCallerEntries;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethod\SpecialMethodInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Exception\OperationProcessorException;

trait Sender
{
    use Validatable;
    use OperandHelper;

    private static SpecialMethodCallerEntries $specialMethodCallerEntries;

    public function processSend(bool $withBlock): ProcessedStatus
    {
        self::$specialMethodCallerEntries ??= new SpecialMethodCallerEntries();

        $callInfo = $this->operandAsCallInfo();

        $block = null;
        if ($withBlock) {
            $iseq = $this
                ->context
                ->kernel()
                ->loadInstructionSequence(
                    new Aux(
                        loader: new AuxLoader(
                            index: $this->operandAsNumber()
                                ->valueOf(),
                        ),
                    ),
                );

            $block = (new Executor(
                kernel: $this->context->kernel(),
                rubyClass: $this->context->self(),
                instructionSequence: $iseq,
                option: $this->context->option(),
                parentContext: $this->context,
            ));
        }

        /**
         * @var StringSymbol $symbol
         */
        $symbol = $callInfo
            ->callData()
            ->mid()
            ->object;

        $arguments = [];
        for ($i = 0; $i < $callInfo->callData()->argumentsCount(); ++$i) {
            $arguments[] = $this->context->vmStack()->pop();
        }

        $this->validateType(
            Operand::class,
            ...$arguments,
        );

        $targetClass = $this->getStack()
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
                $block,
                ...$this->translateForArguments(...$arguments),
            );
        } else {
            /**
             * @var null|ExecutedResult|RubyClassInterface $result
             */
            $result = $targetClass
                ->send(
                    (string) $symbol,
                    $callInfo,
                    $block,

                    // @phpstan-ignore-next-line
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
