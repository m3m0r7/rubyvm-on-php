<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use Throwable;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

class ExecutedResult
{
    public function __construct(
        private readonly ExecutorInterface $executor,
        public readonly ExecutedStatus $executedStatus,
        public readonly ?SymbolInterface $returnValue = null,
        public readonly ?Throwable $throwed = null,
        private readonly ?ExecutorDebugger $debugger = null,
    ) {

    }

    public function debugger(): ExecutorDebugger
    {
        return $this->debugger;
    }
}
