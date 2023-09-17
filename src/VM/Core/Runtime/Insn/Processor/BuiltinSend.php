<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\CallBlockHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Translatable;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

class BuiltinSend implements OperationProcessorInterface
{
    use OperandHelper;
    use Validatable;
    use Translatable;
    use CallBlockHelper;

    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    public function process(mixed ...$arguments): ProcessedStatus
    {
        $callInfo = $this->getOperandAsCallInfo();

        $arguments = [];
        for ($i = 0; $i < $callInfo->callData()->argumentsCount(); ++$i) {
            $arguments[] = $this->context->vmStack()->pop();
        }

        $blockObject = $this->getStack()->operand;

        $this->validateType(
            OperandEntry::class,
            ...$arguments,
        );

        $blockIseqNumber = $this->getOperandAsNumberSymbol();

        $result = $this->callBlockWithArguments(
            $callInfo,
            $blockIseqNumber,
            $blockObject,
            false,
            ...$arguments,
        );

        if ($result !== null) {
            $this->context->vmStack()->push(new OperandEntry($result));
        }

        return ProcessedStatus::SUCCESS;
    }
}
