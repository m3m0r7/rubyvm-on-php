<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Range;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolize;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Exception\OperationProcessorException;

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
        $flags = $this->operandAsNumber();

        /**
         * @var NumberSymbol $high
         */
        $high = $this->stackAsNumber();

        /**
         * @var NumberSymbol $low
         */
        $low = $this->stackAsNumber();

        if (!$high instanceof Symbolize || !$low instanceof Symbolize) {
            throw new OperationProcessorException(
                'The passed value cannot symbolize',
            );
        }

        assert($high->symbol() instanceof NumberSymbol);
        assert($low->symbol() instanceof NumberSymbol);

        $this->context->vmStack()
            ->push(
                new Operand(
                    new Range(new RangeSymbol(
                        begin: $low->symbol(),
                        end: $high->symbol(),
                        excludeEnd: (bool) $flags->valueOf(),
                    )),
                ),
            );

        return ProcessedStatus::SUCCESS;
    }
}
