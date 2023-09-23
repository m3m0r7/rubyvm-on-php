<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\Runtime\Executor\OperationEntries;

class InstructionSequenceBody
{
    public function __construct(
        public readonly InstructionSequenceBodyInterface $data,
        public readonly OperationEntries $operationEntries,
    ) {}
}
