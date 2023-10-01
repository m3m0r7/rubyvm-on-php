<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Exception\OperationProcessorException;

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
        $this->operand();

        $recv = $this->stackAsRubyClass();
        $obj = $this->stackAsAny(RubyClassInterface::class);

        if ($obj instanceof \ArrayAccess) {
            $value = $obj[$recv->valueOf()] ?? null;
        } else {
            throw new OperationProcessorException(
                sprintf(
                    'The %s[%s] cannot access as an array',
                    ClassHelper::nameBy($obj),
                    $recv->valueOf(),
                )
            );
        }

        if (!$value instanceof RubyClassInterface) {
            throw new OperationProcessorException(
                sprintf(
                    'Out of index#%d in the %s',
                    $recv->valueOf(),
                    ClassHelper::nameBy($obj),
                )
            );
        }

        $this->context->vmStack()->push(
            new Operand(
                $value,
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
