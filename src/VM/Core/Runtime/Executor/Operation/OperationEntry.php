<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation;

use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;

class OperationEntry
{
    public function __construct(
        public readonly Insn $insn,
    ) {}
}
