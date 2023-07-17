<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\MainInterface;

class OperationProcessorContext implements ContextInterface
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly ExecutorInterface $executor,
        private readonly MainInterface $main,
        private readonly VMStack $vmStack,
        private readonly ProgramCounter $pc,
        private readonly OperationProcessorEntries $operationProcessorEntries,
        private readonly InstructionSequence $instructionSequence,
        private readonly LoggerInterface $logger,
        private readonly EnvironmentTableEntries $environmentTableEntries,
        private readonly ExecutorDebugger $debugger,
    ) {
    }

    public function createSnapshot(): self
    {
        return new self(
            kernel: clone $this->kernel,
            executor: clone $this->executor,
            main: clone $this->main,
            vmStack: clone $this->vmStack,
            pc: clone $this->pc,
            operationProcessorEntries: clone $this->operationProcessorEntries,
            instructionSequence: clone $this->instructionSequence,
            logger: $this->logger, // NOTE: Do not clone because logger is shared resource
            environmentTableEntries: clone $this->environmentTableEntries,
            debugger: $this->debugger, // NOTE: Do not clone because logger is shared resource
        );
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

    public function environmentTableEntries(): EnvironmentTableEntries
    {
        return $this->environmentTableEntries;
    }

    public function kernel(): KernelInterface
    {
        return $this->kernel;
    }

    public function operationProcessorEntries(): OperationProcessorEntries
    {
        return $this->operationProcessorEntries;
    }

    public function executor(): ExecutorInterface
    {
        return $this->executor;
    }

    public function debugger(): ExecutorDebugger
    {
        return $this->debugger;
    }
}
