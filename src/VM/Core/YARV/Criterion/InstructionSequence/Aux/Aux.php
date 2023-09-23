<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux;

class Aux
{
    public function __construct(
        public readonly AuxLoader $loader,
    ) {}
}
