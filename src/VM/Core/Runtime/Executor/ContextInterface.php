<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\UserlandHeapSpaceInterface;

interface ContextInterface
{
    public function kernel(): KernelInterface;

    public function self(): RubyClassInterface;

    public function vmStack(): VMStack;

    public function programCounter(): ProgramCounter;

    public function logger(): LoggerInterface;

    public function instructionSequence(): InstructionSequence;

    public function environmentTable(): EnvironmentTable;

    public function executor(): ExecutorInterface;

    public function debugger(): ExecutorDebugger;

    public function createSnapshot(): self;

    public function depth(): int;

    public function elapsedTime(): float;

    public function startTime(): float;

    public function shouldBreakPoint(): bool;

    public function shouldProcessedRecords(): bool;

    public function renewEnvironmentTable(): self;

    public function appendTrace(string ...$definitions): self;

    public function traces(): array;
}
