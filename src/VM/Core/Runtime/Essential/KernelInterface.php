<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\VM\Core\Runtime\ID;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offsets;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\RubyVersion;
use RubyVM\VM\Stream\RubyVMBinaryStreamReaderInterface;

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

    public function rubyPlatform(): string;

    public function minorVersion(): int;

    public function majorVersion(): int;

    public function userlandHeapSpace(): UserlandHeapSpaceInterface;

    public function instructionSequenceListSize(): int;

    public function instructionSequenceListOffset(): int;

    public function instructionSequenceList(): Offsets;
}
