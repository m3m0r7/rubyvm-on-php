<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Context;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Debugger\ExecutorDebugger;
use RubyVM\VM\Core\Runtime\Executor\EnvironmentTable;
use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\OptionInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;
use RubyVM\VM\Exception\ContextNotFound;
use RubyVM\VM\Exception\RuntimeException;

class NullContext implements ContextInterface
{
    public function __construct(private readonly KernelInterface $kernel, private readonly RubyClassInterface $rubyClass, private readonly OptionInterface $option) {}

    public function kernel(): KernelInterface
    {
        return $this->kernel;
    }

    public function self(): RubyClassInterface
    {
        return $this->rubyClass;
    }

    public function vmStack(): VMStack
    {
        return new VMStack();
    }

    public function programCounter(): ProgramCounter
    {
        return new ProgramCounter();
    }

    public function logger(): LoggerInterface
    {
        return $this->option->logger();
    }

    public function instructionSequence(): InstructionSequenceInterface
    {
        throw new RuntimeException(
            'The NullContext cannot be executable',
        );
    }

    public function environmentTable(): EnvironmentTable
    {
        return new EnvironmentTable();
    }

    public function executor(): ExecutorInterface
    {
        throw new RuntimeException(
            'The NullContext cannot be executable',
        );
    }

    public function debugger(): ExecutorDebugger
    {
        return new ExecutorDebugger();
    }

    public function createSnapshot(): ContextInterface
    {
        return $this;
    }

    public function depth(): int
    {
        return 0;
    }

    public function elapsedTime(): float
    {
        return 0;
    }

    public function startTime(): float
    {
        return 0;
    }

    public function shouldBreakPoint(): bool
    {
        return false;
    }

    public function shouldProcessedRecords(): bool
    {
        return false;
    }

    public function renewEnvironmentTable(): ContextInterface
    {
        return $this;
    }

    public function appendTrace(string ...$definitions): ContextInterface
    {
        return $this;
    }

    public function traces(): array
    {
        return [];
    }

    public function IOContext(): IOContext
    {
        return new IOContext(
            $this->option->stdOut(),
            $this->option->stdOut(),
            $this->option->stdOut(),
        );
    }

    public function parentContext(): ?ContextInterface
    {
        return null;
    }

    public function option(): OptionInterface
    {
        return $this->option;
    }

    public function modulePath(string $path = null): string
    {
        return '';
    }

    public function setCallInfo(CallInfoInterface $callInfo): self
    {
        return $this;
    }

    public function callInfo(): ?CallInfoInterface
    {
        return null;
    }
}
