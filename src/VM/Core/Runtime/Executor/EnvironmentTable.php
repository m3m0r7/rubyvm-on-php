<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Helper\DebugFormat;
use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

class EnvironmentTable extends AbstractEntries
{
    use DebugFormat;

    public function verify(mixed $value): bool
    {
        return $value instanceof Object_;
    }
}
