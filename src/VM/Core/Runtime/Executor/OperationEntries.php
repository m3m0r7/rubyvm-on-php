<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Symbol\Object_;

class OperationEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof OperationEntry ||
            $value instanceof OperandEntry ||
            $value instanceof UnknownEntry;
    }
}
