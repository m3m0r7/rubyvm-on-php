<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

interface ExecutorInterface
{
    public function execute(...$arguments): ExecutedResult;

    public function enableBreakpoint(bool $enabled = true): self;

    public function enableProcessedRecords(bool $enabled = true): self;

    public function context(): ContextInterface;

    public function createContext(?ContextInterface $previousContext = null): ContextInterface;
}
