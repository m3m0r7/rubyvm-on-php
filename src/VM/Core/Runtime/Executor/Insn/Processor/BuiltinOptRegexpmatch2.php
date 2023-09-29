<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinOptRegexpmatch2 implements OperationProcessorInterface
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

    /**
     * @see https://docs.ruby-lang.org/ja/latest/class/Regexp.html#I_--3D--7E
     */
    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        $callInfo = $this->getOperandAsCallInfo();

        $source = $this->getStackAsStringOrNil();
        $regexp = $this->getStackAsRegExp();

        $this->context->vmStack()->push(
            new Operand(
                $regexp
                    ->toBeRubyClass()
                    ->setRuntimeContext($this->context)
                    // Call =~ instance method. but an internal calls equalsTilde function on PHP
                    ->{'=~'}($callInfo, $source)
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
