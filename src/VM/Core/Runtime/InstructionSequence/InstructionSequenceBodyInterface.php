<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\InstructionSequence;

use RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence\InstructionSequenceCompileData;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence\ObjectParameter;

interface InstructionSequenceBodyInterface
{
    public function type(): int;

    public function stackMax(): int;

    public function inlineCacheSize(): int;

    public function localTableSize(): int;

    public function compileData(): InstructionSequenceCompileData;

    public function parentInstructionSequence(): ?InstructionSequenceInterface;

    public function objectParam(): ObjectParameter;
}
