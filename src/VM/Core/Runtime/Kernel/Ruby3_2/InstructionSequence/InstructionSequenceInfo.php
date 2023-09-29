<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\Runtime\Executor\Operation\OperationEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\CallInfoEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\CatchEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\OuterVariableEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\VariableEntries;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InsnsInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInfoInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\LocationInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\ObjectParameterInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\VariableInterface;
use RubyVM\VM\Exception\RubyVMException;

class InstructionSequenceInfo implements InstructionSequenceInfoInterface
{
    protected InstructionSequenceCompileData $compileData;

    protected ?CallInfoInterface $currentCallInfo = null;

    public function __construct(
        public readonly int $type,
        public readonly int $stackMax,
        public readonly int $iseqSize,
        public readonly ObjectParameterInterface $objectParam,
        public readonly int $localTableSize,
        public readonly int $ciSize,
        public readonly InsnsInterface $insns,
        public readonly VariableInterface $variable,
        public readonly LocationInterface $location,
        public readonly int $catchExceptP,
        public readonly int $builtinInlineP,
        public readonly int $ivcSize,
        public readonly int $icvArcSize,
        public readonly int $iseSize,
        public readonly int $icSize,
        public readonly OuterVariableEntries $outerVariables,
        public readonly VariableEntries $variableEntries,
        public readonly CatchEntries $catchTable,
        public readonly ?InstructionSequenceInterface $parentISeq,
        public readonly ?InstructionSequenceInterface $localISeq,
        public readonly ?InstructionSequenceInterface $mandatoryOnlyISeq,
        public readonly CallInfoEntries $callInfoEntries,
        public readonly int $bytecodeOffset,
        public readonly int $bytecodeSize,
        private ?OperationEntries $operationEntries = null,
    ) {
        $this->compileData = new InstructionSequenceCompileData();
    }

    public function setOperationEntries(OperationEntries $entries): InstructionSequenceInfoInterface
    {
        $this->operationEntries = $entries;

        return $this;
    }

    public function operationEntries(): OperationEntries
    {
        return $this->operationEntries ?? throw new RubyVMException('The operation entry was not set - did you forget to call InstructionSequenceInfo::setOperationSentries before?');
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

    public function objectParam(): ObjectParameterInterface
    {
        return $this->objectParam;
    }

    public function variables(): VariableEntries
    {
        return $this->variableEntries;
    }

    public function callInfoEntries(): CallInfoEntries
    {
        return $this->callInfoEntries;
    }

    public function catchEntries(): CatchEntries
    {
        return $this->catchTable;
    }
}
