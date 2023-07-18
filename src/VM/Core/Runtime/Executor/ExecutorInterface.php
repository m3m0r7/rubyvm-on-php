<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

interface ExecutorInterface
{
    public function execute(): ExecutedResult;

    public function breakPoint(): bool;
    public function enableBreakpoint(bool $enabled = true): self;

}
