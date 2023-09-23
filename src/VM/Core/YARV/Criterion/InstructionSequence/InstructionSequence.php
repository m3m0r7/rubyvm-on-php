<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\Runtime\Executor\Operation\OperationEntries;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;

class InstructionSequence implements InstructionSequenceInterface
{
    private ?InstructionSequenceBody $body = null;

    public function __construct(
        public readonly Aux $aux,
        private readonly InstructionSequenceProcessorInterface $processor,
    ) {}

    public function load(): void
    {
        $this->body = $this->processor->process();
    }

    public function operations(): OperationEntries
    {
        return $this->body->operationEntries;
    }

    public function body(): ?InstructionSequenceBody
    {
        return $this->body;
    }

    public function index(): int
    {
        return $this->aux->loader->index;
    }
}
