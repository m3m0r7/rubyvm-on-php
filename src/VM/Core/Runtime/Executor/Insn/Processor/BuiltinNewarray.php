<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Entity\Array_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;

class BuiltinNewarray implements OperationProcessorInterface
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
        $entries = [];
        $num = $this->getOperandAsNumber();

        for ($i = $num->valueOf() - 1; $i >= 0; --$i) {
            $entries[$i] = ($this->getStackAsNumber())->symbol();
        }

        $this->context->vmStack()->push(
            new Operand(
                (new Array_(new ArraySymbol(array_values($entries))))
                    ->toRubyClass(),
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
