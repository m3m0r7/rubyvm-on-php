<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\Runtime\Executor\Operation\OperationEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\CallInfoEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\CatchEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\VariableEntries;

interface InstructionSequenceInfoInterface
{
    public function type(): int;

    public function stackMax(): int;

    public function inlineCacheSize(): int;

    public function localTableSize(): int;

    public function variables(): VariableEntries;

    public function compileData(): InstructionSequenceCompileDataInterface;

    public function parentInstructionSequence(): ?InstructionSequenceInterface;

    public function objectParam(): ObjectParameterInterface;

    public function setOperationEntries(OperationEntries $entries): InstructionSequenceInfoInterface;

    public function operationEntries(): OperationEntries;

    public function callInfoEntries(): CallInfoEntries;

    public function catchEntries(): CatchEntries;

    public function path(): string;
}
