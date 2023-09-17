<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Insn\Insn;

class OperationEntry
{
    public function __construct(
        public readonly Insn $insn,
    ) {}
}
