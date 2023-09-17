<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\RubyClassImplementationInterface;

class OperationProcessorContext implements ContextInterface
{
    private float $startTime = 0.0;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly ExecutorInterface $executor,
        private readonly RubyClassImplementationInterface $classImplementation,
        private readonly VMStack $vmStack,
        private readonly ProgramCounter $pc,
        private readonly InstructionSequence $instructionSequence,
        private readonly LoggerInterface $logger,
        private EnvironmentTable $environmentTable,
        private readonly ExecutorDebugger $debugger,
        private readonly int $depth,
        ?float $startTime,
        private readonly bool $shouldProcessedRecords,
        private readonly bool $shouldBreakPoint,
        private array $traces = [],
    ) {
        $this->startTime = $startTime ?? microtime(true);
    }

    public function renewEnvironmentTable(): self
    {
        $this->environmentTable = new EnvironmentTable();

        return $this;
    }

    public function startTime(): float
    {
        return $this->startTime;
    }

    public function elapsedTime(): float
    {
        return microtime(true) - $this->startTime;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function createSnapshot(): self
    {
        return new self(
            kernel: clone $this->kernel,
            executor: clone $this->executor,
            classImplementation: clone $this->classImplementation,
            vmStack: clone $this->vmStack,
            pc: clone $this->pc,
            instructionSequence: clone $this->instructionSequence,
            logger: $this->logger, // NOTE: Do not clone because logger is shared resource
            environmentTable: clone $this->environmentTable,
            debugger: $this->debugger, // NOTE: Do not clone because logger is shared resource
            depth: $this->depth,
            startTime: $this->startTime,
            shouldProcessedRecords: $this->shouldProcessedRecords,
            shouldBreakPoint: $this->shouldBreakPoint,
            traces: $this->traces,
        );
    }

    public function self(): RubyClassImplementationInterface
    {
        return $this->classImplementation;
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

    public function environmentTable(): EnvironmentTable
    {
        return $this->environmentTable;
    }

    public function kernel(): KernelInterface
    {
        return $this->kernel;
    }

    public function executor(): ExecutorInterface
    {
        return $this->executor;
    }

    public function debugger(): ExecutorDebugger
    {
        return $this->debugger;
    }

    public function shouldProcessedRecords(): bool
    {
        return $this->shouldProcessedRecords;
    }

    public function shouldBreakPoint(): bool
    {
        return $this->shouldBreakPoint;
    }

    public function appendTrace(string ...$definitions): ContextInterface
    {
        array_push($this->traces, ...$definitions);

        return $this;
    }

    public function traces(): array
    {
        return $this->traces;
    }
}
