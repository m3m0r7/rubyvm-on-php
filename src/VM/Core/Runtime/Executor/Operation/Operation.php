<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation;

use RubyVM\VM\Core\Runtime\Executor\Insn\InsnInterface;

class Operation
{
    public function __construct(
        public readonly InsnInterface $insn,
    ) {}
}
