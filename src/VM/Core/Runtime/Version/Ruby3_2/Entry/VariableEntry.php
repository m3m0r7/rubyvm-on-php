<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry;

use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\ID;

class VariableEntry
{
    public function __construct(
        public readonly ID $id
    ) {}
}
