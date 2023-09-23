<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Executor\Accessor\ContextAccessor;
use RubyVM\VM\Core\Runtime\Executor\Accessor\ContextAccessorInterface;
use RubyVM\VM\Core\YARV\Criterion\Essential\RubyClassInterface;

readonly class ExecutedResult
{
    public function __construct(
        public ExecutorInterface $executor,
        public ExecutedStatus $executedStatus,
        public RubyClassInterface|null $returnValue = null,
        public ?\Throwable $threw = null,
        private ?ExecutorDebugger $debugger = null,
    ) {}

    public function context(): ContextAccessorInterface
    {
        return new ContextAccessor($this);
    }

    public function debugger(): ExecutorDebugger
    {
        return $this->debugger;
    }
}
