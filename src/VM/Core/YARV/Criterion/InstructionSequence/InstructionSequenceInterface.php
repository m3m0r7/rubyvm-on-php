<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\Runtime\Executor\Operation\OperationEntries;

interface InstructionSequenceInterface
{
    public function body(): ?InstructionSequenceBody;

    public function operations(): OperationEntries;

    public function load(): void;

    public function index(): int;
}
