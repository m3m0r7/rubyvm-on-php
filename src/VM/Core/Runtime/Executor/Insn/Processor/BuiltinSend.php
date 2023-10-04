<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\CallBlockHelper;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\ArgumentTransformable;
use RubyVM\VM\Core\Runtime\Executor\Validatable;

class BuiltinSend implements OperationProcessorInterface
{
    use OperandHelper;
    use Validatable;
    use ArgumentTransformable;
    use CallBlockHelper;

    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(): ProcessedStatus
    {
        $callInfo = $this->operandAsCallInfo();

        $arguments = [];
        for ($i = 0; $i < $callInfo->callData()->argumentsCount(); ++$i) {
            $arguments[] = $this->context->vmStack()->pop();
        }

        $this->validateType(
            Operand::class,
            ...$arguments,
        );

        $blockObject = $this->stackAsRubyClass();

        $blockIseqNumber = $this->operandAsNumber();

        $result = $this->callBlockWithArguments(
            $callInfo,
            $blockIseqNumber,
            $blockObject,
            false,

            // @phpstan-ignore-next-line
            ...$this->translateForArguments(
                ...$arguments,
            ),
        );

        if ($result instanceof RubyClassInterface) {
            $this->context->vmStack()->push(new Operand($result));
        }

        return ProcessedStatus::SUCCESS;
    }
}
