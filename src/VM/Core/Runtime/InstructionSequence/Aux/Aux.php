<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\InstructionSequence\Aux;

class Aux
{
    public function __construct(
        public readonly AuxLoader $loader,
    ) {
    }
}
