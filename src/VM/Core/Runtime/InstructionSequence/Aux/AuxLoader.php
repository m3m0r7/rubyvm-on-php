<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\InstructionSequence\Aux;

class AuxLoader
{
    public function __construct(
        public readonly int $obj,
        public readonly int $index,
    ){
    }
}
