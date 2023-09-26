<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Context;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Debugger\ExecutorDebugger;
use RubyVM\VM\Core\Runtime\Executor\EnvironmentTable;
use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\OptionInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;
use RubyVM\VM\Exception\RuntimeException;

class OperationProcessorContext implements ContextInterface
{
    private float $startTime = 0.0;

    /**
     * @param string[] $traces
     */
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly ExecutorInterface $executor,
        private readonly RubyClassInterface $rubyClass,
        private readonly VMStack $vmStack,
        private readonly ProgramCounter $pc,
        private readonly InstructionSequenceInterface $instructionSequence,
        private readonly OptionInterface $option,
        private readonly IOContext $IOContext,
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

    public function __debugInfo(): array
    {
        return [];
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
            rubyClass: clone $this->rubyClass,
            vmStack: clone $this->vmStack,
            pc: clone $this->pc,
            instructionSequence: clone $this->instructionSequence,
            option: $this->option,
            IOContext: $this->IOContext,
            environmentTable: clone $this->environmentTable,
            debugger: $this->debugger, // NOTE: Do not clone because logger is shared resource
            depth: $this->depth,
            startTime: $this->startTime,
            shouldProcessedRecords: $this->shouldProcessedRecords,
            shouldBreakPoint: $this->shouldBreakPoint,
            traces: $this->traces,
        );
    }

    public function self(): RubyClassInterface
    {
        return $this->rubyClass;
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
        return $this->option->logger();
    }

    public function option(): OptionInterface
    {
        return $this->option;
    }

    public function instructionSequence(): InstructionSequenceInterface
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

    /**
     * @return string[]
     */
    public function traces(): array
    {
        return $this->traces;
    }

    public function IOContext(): IOContext
    {
        return $this->IOContext;
    }

    public function modulePath(string $path = null): string
    {
        $paths = [];
        $classContext = null;
        $remainingCounter = Option::MAX_STACK_EXCEEDED;
        $roots = Option::MAIN_CONTEXT_CLASS;

        do {
            $self = ($classContext ?? $this)->self();

            $classContext = $self->context();

            $paths = [(string) $self->entity(), ...$paths];
            $remainingCounter--;
            if ($remainingCounter === 0) {
                throw new RuntimeException('Cannot resolve module path because max stack exceeded');
            }
        } while (!$self instanceof $roots);

        if ($path !== null) {
            $paths[] = $path;
        }

        return implode('.', $paths);
    }
}
