<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Entry;

use RubyVM\VM\Core\YARV\Essential\Symbol\ID;

class VariableEntry
{
    public function __construct(
        public readonly ID $id
    ) {}
}
