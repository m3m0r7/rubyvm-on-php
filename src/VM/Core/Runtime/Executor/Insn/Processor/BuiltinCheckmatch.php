<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\CheckMatchType;
use RubyVM\VM\Core\Runtime\Entity\Boolean_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinCheckmatch implements OperationProcessorInterface
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
        // 1 ... WHEN
        // 2 ... CASE
        // 3 ... RESCUE
        $type = $this->getOperandAsNumber();

        // TODO: We will implement other types
        if ($type->valueOf() !== CheckMatchType::RESCUE->value) {
            throw new OperationProcessorException(sprintf('The `%s` (opcode: 0x%02x) processor with %d type is not implemented yet', strtolower($this->insn->name), $this->insn->value, $type->valueOf()));
        }

        $compareBy = $this->getStackAsRubyClass();
        $compareFrom = $this->getStackAsRubyClass();

        $this->context
            ->vmStack()
            ->push(new Operand(
                Boolean_::createBy(
                    $compareBy->entity()->valueOf() === $compareFrom->entity()->valueOf()
                )->toBeRubyClass(),
            ));

        return ProcessedStatus::SUCCESS;
    }
}
