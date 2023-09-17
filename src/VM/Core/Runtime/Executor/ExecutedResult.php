<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Executor\Accessor\AccessorInterface;
use RubyVM\VM\Core\Runtime\Executor\Accessor\Method;
use RubyVM\VM\Core\Runtime\RubyClassImplementationInterface;
use RubyVM\VM\Core\Runtime\Symbol\Object_;

class ExecutedResult
{
    public function __construct(
        public readonly ExecutorInterface $executor,
        public readonly ExecutedStatus $executedStatus,
        public readonly Object_|RubyClassImplementationInterface|null $returnValue = null,
        public readonly ?\Throwable $threw = null,
        private readonly ?ExecutorDebugger $debugger = null,
    ) {
    }

    public function methods(): AccessorInterface
    {
        return new Method($this);
    }

    public function debugger(): ExecutorDebugger
    {
        return $this->debugger;
    }
}
