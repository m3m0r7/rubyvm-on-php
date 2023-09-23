<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Entity\Number;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
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

        $recv = $this->getStackAsNumber();
        $obj = $this->getStackAsEntity();

        /**
         * @var NumberSymbol $selectedNumber
         */
        $selectedNumber = $obj->symbol()[$recv->valueOf()];

        $this->context->vmStack()->push(
            new Operand(
                (new Number($selectedNumber))
                    ->toRubyClass(),
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
