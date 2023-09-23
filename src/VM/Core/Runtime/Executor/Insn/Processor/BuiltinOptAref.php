<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\YARV\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;

class BuiltinOptAref implements OperationProcessorInterface
{
    use OperandHelper;
    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        // No used (This operand is only array always; which calls [] in the ruby and refs array symbol)
        $this->getOperand();

        $recv = $this->getStackAsNumberSymbol();
        $obj = $this->getStackAsSymbol();

        /**
         * @var NumberSymbol $selectedNumber
         */
        $selectedNumber = $obj[$recv->valueOf()];

        $this->context->vmStack()->push(
            new OperandEntry(
                $selectedNumber
                    ->toRubyClass(),
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
