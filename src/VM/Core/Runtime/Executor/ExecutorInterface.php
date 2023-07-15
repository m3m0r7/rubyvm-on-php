<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

interface ExecutorInterface
{
    public function execute(): ExecutedStatus;
    public function debugger(): ExecutorDebugger;
}
