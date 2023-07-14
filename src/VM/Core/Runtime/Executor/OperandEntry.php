<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Symbol\Object_;

class OperandEntry
{
    public function __construct(
        public readonly Object_|CallInfoEntryInterface $operand
    ) {
    }
}
