<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Entry;

use RubyVM\VM\Core\YARV\Essential\ID;

class OuterVariable
{
    public function __construct(
        public readonly ID $key,
        protected readonly int $value,
    ) {}
}
