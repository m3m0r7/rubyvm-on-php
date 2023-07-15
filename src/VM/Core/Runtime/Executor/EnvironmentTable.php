<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Symbol\Object_;

class EnvironmentTable extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof Object_;
    }
}
