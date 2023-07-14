<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\ProgramCounter;
use RubyVM\VM\Core\Runtime\Executor\VMStack;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinPutobjectINT2FIX1 implements OperationProcessorInterface
{
    private Insn $insn;

    private ProgramCounter $pc;
    private VMStack $VMStack;
    private LoggerInterface $logger;

    public function prepare(Insn $insn, ProgramCounter $pc, VMStack $VMStack, LoggerInterface $logger): void
    {
        $this->insn = $insn;
        $this->pc = $pc;
        $this->VMStack = $VMStack;
        $this->logger = $logger;
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
