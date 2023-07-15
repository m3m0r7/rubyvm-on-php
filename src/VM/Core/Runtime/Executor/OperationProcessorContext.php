<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\MainInterface;

class OperationProcessorContext implements ContextInterface
{
    public function __construct(
        private MainInterface $main,
        private VMStack $vmStack,
        private ProgramCounter $pc,
        private InstructionSequence $instructionSequence,
        private LoggerInterface $logger,
    ) {
    }

    public function __clone()
    {
        $this->main = clone $this->main;
        $this->vmStack = clone $this->vmStack;
        $this->pc = clone $this->pc;
        $this->instructionSequence = clone $this->instructionSequence;
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
