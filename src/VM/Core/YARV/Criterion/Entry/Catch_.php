<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Entry;

use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CatchInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;
use RubyVM\VM\Exception\RuntimeException;

class Catch_ implements CatchInterface
{
    public function __construct(
        private readonly KernelInterface $kernel,
        protected int $iseqIndex,
        protected int $type,
        protected int $start,
        protected int $end,
        protected int $cont,
        protected int $sp,
    ) {}

    public function instructionSequence(): InstructionSequenceInterface
    {
        if ($this->iseqIndex === -1) {
            throw new RuntimeException(
                sprintf(
                    'Not found specified number of instruction sequence#%d',
                    $this->iseqIndex,
                ),
            );
        }

        return $this->kernel->loadInstructionSequence(new Aux(
            loader: new AuxLoader(
                index: $this->iseqIndex,
            ),
        ));
    }

    public function start(): int
    {
        return $this->start;
    }

    public function end(): int
    {
        return $this->end;
    }

    public function cont(): int
    {
        return $this->cont;
    }
}
