<?php
declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\MainInterface;

interface ContextInterface
{
    public function self(): MainInterface;
    public function vmStack(): VMStack;
    public function programCounter(): ProgramCounter;
    public function logger(): LoggerInterface;
    public function instructionSequence(): InstructionSequence;
}
