<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

interface MainInterface
{
    public function puts(SymbolInterface $symbol): void;
    public function def(StringSymbol $methodName, InstructionSequence $instructionSequence): void;
}
