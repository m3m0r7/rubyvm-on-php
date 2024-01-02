<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\Executor\Debugger\DebuggerInterface;
use RubyVM\Stream\StreamHandlerInterface;

interface OptionInterface
{
    public function entryPointIndex(): int;

    public function logger(): LoggerInterface;

    public function stdOut(): StreamHandlerInterface;

    public function stdIn(): StreamHandlerInterface;

    public function stdErr(): StreamHandlerInterface;

    public function debugger(): DebuggerInterface;
}
