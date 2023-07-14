<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\ProgramCounter;
use RubyVM\VM\Core\Runtime\Executor\VMStack;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinDupn implements OperationProcessorInterface
{
    private Insn $insn;

    private ProgramCounter $pc;
    private VMStack $VMStack;

    public function prepare(Insn $insn, ProgramCounter $pc, VMStack $VMStack): void
    {
        $this->insn = $insn;
        $this->pc = $pc;
        $this->VMStack = $VMStack;
    }

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    public function process(): ProcessedStatus
    {
        throw new OperationProcessorException(
            sprintf(
                'The `%s` (opcode: 0x%02x) processor is not implemented yet',
                strtolower($this->insn->name),
                $this->insn->value,
            )
        );
    }
}
