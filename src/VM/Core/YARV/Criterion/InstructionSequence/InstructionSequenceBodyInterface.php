<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

interface InstructionSequenceBodyInterface
{
    public function type(): int;

    public function stackMax(): int;

    public function inlineCacheSize(): int;

    public function localTableSize(): int;

    public function compileData(): InstructionSequenceCompileDataInterface;

    public function parentInstructionSequence(): ?InstructionSequenceInterface;

    public function objectParam(): ObjectParameterInterface;
}
