<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Exception\RubyVMException;

class InstructionSequence implements InstructionSequenceInterface
{
    private ?InstructionSequenceBodyInterface $body = null;

    public function __construct(
        public readonly Aux $aux,
        private readonly InstructionSequenceProcessorInterface $processor,
    ) {}

    public function load(): void
    {
        $this->body = $this->processor->process();
    }

    public function body(): InstructionSequenceBodyInterface
    {
        return $this->body ?? throw new RubyVMException(
            sprintf(
                'The instruction sequence (#%d) was not loaded - did you forget to call InstructionSequence::load before?',
                $this->aux->loader->index,
            )
        );
    }

    public function index(): int
    {
        return $this->aux->loader->index;
    }
}
