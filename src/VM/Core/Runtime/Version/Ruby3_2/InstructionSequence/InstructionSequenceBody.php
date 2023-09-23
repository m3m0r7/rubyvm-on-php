<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\Runtime\Executor\CallInfoEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\CatchEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\OuterVariableEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\VariableEntries;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InsnsInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceBodyInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\LocationInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\ObjectParameterInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\VariableInterfce;

class InstructionSequenceBody implements InstructionSequenceBodyInterface
{
    protected InstructionSequenceCompileData $compileData;

    public function __construct(
        public readonly int $type,
        public readonly int $stackMax,
        public readonly int $iseqSize,
        public readonly ObjectParameterInterface $objectParam,
        public readonly int $localTableSize,
        public readonly int $ciSize,
        public readonly InsnsInterface $insns,
        public readonly VariableInterfce $variable,
        public readonly LocationInterface $location,
        public readonly int $catchExceptP,
        public readonly int $builtinInlineP,
        public readonly int $ivcSize,
        public readonly int $icvArcSize,
        public readonly int $iseSize,
        public readonly int $icSize,
        public readonly OuterVariableEntries $outerVariables,
        public readonly VariableEntries $localTable,
        public readonly CatchEntries $catchTable,
        public readonly ?InstructionSequenceInterface $parentISeq,
        public readonly ?InstructionSequenceInterface $localISeq,
        public readonly ?InstructionSequenceInterface $mandatoryOnlyISeq,
        public readonly CallInfoEntries $callInfoEntries,
        public readonly int $bytecodeOffset,
        public readonly int $bytecodeSize,
    ) {
        $this->compileData = new InstructionSequenceCompileData();
    }

    public function compileData(): InstructionSequenceCompileData
    {
        return $this->compileData;
    }

    public function type(): int
    {
        return $this->type;
    }

    public function stackMax(): int
    {
        return $this->stackMax;
    }

    public function inlineCacheSize(): int
    {
        return $this->icSize;
    }

    public function parentInstructionSequence(): ?InstructionSequenceInterface
    {
        return $this->parentISeq;
    }

    public function localTableSize(): int
    {
        return $this->localTableSize;
    }

    public function objectParam(): ObjectParameter
    {
        return $this->objectParam;
    }
}
