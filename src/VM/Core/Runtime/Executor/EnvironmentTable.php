<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\DebugFormat;
use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Exception\LocalTableException;

class EnvironmentTable extends AbstractEntries
{
    use DebugFormat;

    public function verify(mixed $value): bool
    {
        return $value instanceof Object_;
    }

    public function get(mixed $index): mixed
    {
        if (!$this->has($index)) {
            throw new LocalTableException(
                sprintf(
                    'Failed to get from the LocalTable#%d because specified index is out of bound in the local table entries',
                    $index,
                ),
            );
        }
        return parent::get($index);
    }
}
