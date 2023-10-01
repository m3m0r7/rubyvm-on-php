<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Accessor\ContextAccessor;
use RubyVM\VM\Core\Runtime\Executor\Accessor\ContextAccessorInterface;

readonly class ExecutedResult
{
    public function __construct(
        public ExecutorInterface $executor,
        public ExecutedStatus $executedStatus,
        public RubyClassInterface|null $returnValue = null,
        public ?\Throwable $threw = null,
    ) {}

    public function context(): ContextAccessorInterface
    {
        return new ContextAccessor($this);
    }
}
