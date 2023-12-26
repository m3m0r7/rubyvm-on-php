<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Hash;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\InsnInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinNewhash implements OperationProcessorInterface
{
    use OperandHelper;
    private InsnInterface $insn;

    private ContextInterface $context;

    public function prepare(InsnInterface $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(): ProcessedStatus
    {
        $number = $this->operandAsNumber();
        $newHash = new Hash();
        for ($i = 0; $i < ($number->valueOf() / 2); ++$i) {
            $value = $this->stackAsRubyClass();
            $name = $this->stackAsSymbol();

            $newHash[(string) $name] = $value;
        }

        $this->context->vmStack()->push(
            new Operand(
                $newHash,
            )
        );

        return ProcessedStatus::SUCCESS;
    }
}
