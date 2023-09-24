<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux;

class AuxLoader
{
    public function __construct(
        public readonly int $index,
        public readonly ?int $obj = null,
    ) {}
}
