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
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;

interface ContextInterface
{
    public function kernel(): KernelInterface;

    public function self(): RubyClassInterface;

    public function vmStack(): VMStack;

    public function programCounter(): ProgramCounter;

    public function logger(): LoggerInterface;

    public function instructionSequence(): InstructionSequenceInterface;

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

    /**
     * @return string[]
     */
    public function traces(): array;

    public function IOContext(): IOContext;

    public function option(): OptionInterface;
}
