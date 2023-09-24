<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Entity\Range;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;

class BuiltinNewrange implements OperationProcessorInterface
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
        $flags = $this->getOperandAsNumber();

        /**
         * @var NumberSymbol $high
         */
        $high = $this->getStackAsNumber()->symbol();

        /**
         * @var NumberSymbol $low
         */
        $low = $this->getStackAsNumber()->symbol();

        $this->context->vmStack()
            ->push(
                new Operand(
                    (new Range(new RangeSymbol(
                        begin: $low,
                        end: $high,
                        excludeEnd: (bool) $flags->valueOf(),
                    )))->toBeRubyClass(),
                ),
            );

        return ProcessedStatus::SUCCESS;
    }
}
