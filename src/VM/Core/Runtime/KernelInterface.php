<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\DefinedClassEntries;
use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethod\ClassExtender;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorEntries;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;

interface KernelInterface
{
    public function setup(): KernelInterface;

    public function process(): ExecutorInterface;

    /**
     * @return RubyVersion[]
     */
    public function expectedVersions(): array;

    public function stream(): BinaryStreamReaderInterface;

    public function findId(int $index): ID;

    public function findObject(int $index): Object_;

    public function loadInstructionSequence(Aux $aux): InstructionSequence;

    public function classExtender(): ClassExtender;

    public function operationProcessorEntries(): OperationProcessorEntries;
}
