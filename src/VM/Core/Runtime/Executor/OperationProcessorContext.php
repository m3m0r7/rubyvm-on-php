<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\MainInterface;

class OperationProcessorContext implements ContextInterface
{
    public function __construct(
        private readonly MainInterface $main,
        private readonly VMStack $vmStack,
        private readonly ProgramCounter $pc,
        private readonly InstructionSequence $instructionSequence,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function self(): MainInterface
    {
        return $this->main;
    }

    public function vmStack(): VMStack
    {
        return $this->vmStack;
    }

    public function programCounter(): ProgramCounter
    {
        return $this->pc;
    }

    public function logger(): LoggerInterface
    {
        return $this->logger;
    }

    public function instructionSequence(): InstructionSequence
    {
        return $this->instructionSequence;
    }
}
