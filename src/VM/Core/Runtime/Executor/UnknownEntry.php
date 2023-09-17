<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

class UnknownEntry
{
    public function __construct(
        public readonly mixed $data,
        public readonly string $type,
    ) {}
}
