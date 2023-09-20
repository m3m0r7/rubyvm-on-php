<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Executor\IOContext;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorEntries;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Stream\RubyVMBinaryStreamReaderInterface;

interface KernelInterface
{
    public function setup(): KernelInterface;

    public function process(): ExecutorInterface;

    /**
     * @return RubyVersion[]
     */
    public function expectedVersions(): array;

    public function stream(): RubyVMBinaryStreamReaderInterface;

    public function findId(int $index): ID;

    public function findObject(int $index): Object_;

    public function loadInstructionSequence(Aux $aux): InstructionSequence;

    public function operationProcessorEntries(): OperationProcessorEntries;

    public function IOContext(): IOContext;

    public function extraData(): string;

    public function rubyPlatform(): string;

    public function minorVersion(): int;

    public function majorVersion(): int;

    public function userlandHeapSpace(): UserlandHeapSpace;
}
