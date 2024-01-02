<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\RubyVersion;
use RubyVM\Stream\RubyVMBinaryStreamReaderInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorEntries;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offsets;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;
use RubyVM\VM\Core\YARV\Essential\ID;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

interface KernelInterface
{
    public function setup(): KernelInterface;

    /**
     * @return RubyVersion[]
     */
    public function expectedVersions(): array;

    public function stream(): RubyVMBinaryStreamReaderInterface;

    public function findId(int $index): ID;

    public function findObject(int $index): SymbolInterface;

    public function loadInstructionSequence(Aux $aux): InstructionSequence;

    public function extraData(): string;

    public function rubyPlatform(): ?string;

    public function minorVersion(): int;

    public function majorVersion(): int;

    public function size(): int;

    public function extraSize(): int;

    public function userlandHeapSpace(): UserlandHeapSpaceInterface;

    public function magic(): string;

    public function instructionSequenceListSize(): int;

    public function instructionSequenceListOffset(): int;

    public function globalObjectListSize(): int;

    public function globalObjectListOffset(): int;

    public function instructionSequenceList(): Offsets;

    public function operationProcessorEntries(): OperationProcessorEntries;
}
