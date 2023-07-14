<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\InstructionSequence;

use RubyVM\VM\Core\Runtime\Executor\CallInfoEntries;
use RubyVM\VM\Stream\BinaryStreamReader;

interface InstructionSequenceBodyInterface
{
    public function type(): int;
    public function stackMax(): int;
}
