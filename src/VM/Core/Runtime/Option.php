<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\Essential\MainInterface;
use RubyVM\VM\Core\Runtime\Executor\Debugger\DebuggerInterface;
use RubyVM\VM\Core\Runtime\Executor\Debugger\DefaultExecutorDebugger;
use RubyVM\Stream\BinaryStreamReaderInterface;
use RubyVM\Stream\StreamHandler;
use RubyVM\Stream\StreamHandlerInterface;

class Option implements OptionInterface
{
    final public const DETECT_INFINITY_LOOP = 3;

    final public const MAX_TIME_EXCEEDED = 5;

    final public const MAX_STACK_EXCEEDED = 30;

    final public const RUBY_ENCINDEX_BUILTIN_MAX = 12;

    final public const VM_ENV_DATA_INDEX_FLAGS = 2;

    final public const VM_ENV_DATA_INDEX_SPECVAL = 1;

    final public const VM_ENV_DATA_INDEX_ME_CREF = 0;

    final public const VM_ENV_DATA_SIZE = 3;

    final public const DEFAULT_ENTRYPOINT_AUX_INDEX = 0;

    final public const MAIN_CONTEXT_CLASS = MainInterface::class;

    public function __construct(
        public readonly BinaryStreamReaderInterface $reader,
        public readonly LoggerInterface $logger,
        private ?StreamHandlerInterface $stdOut = null,
        private ?StreamHandlerInterface $stdIn = null,
        private ?StreamHandlerInterface $stdErr = null,
        private ?DebuggerInterface $debugger = null,
    ) {}

    public function entryPointIndex(): int
    {
        return static::DEFAULT_ENTRYPOINT_AUX_INDEX;
    }

    public function logger(): LoggerInterface
    {
        return $this->logger;
    }

    public function stdOut(): StreamHandlerInterface
    {
        return $this->stdOut ??= new StreamHandler(STDOUT);
    }

    public function stdIn(): StreamHandlerInterface
    {
        return $this->stdIn ??= new StreamHandler(STDIN);
    }

    public function stdErr(): StreamHandlerInterface
    {
        return $this->stdErr ??= new StreamHandler(STDERR);
    }

    public function debugger(): DebuggerInterface
    {
        return $this->debugger ??= new DefaultExecutorDebugger();
    }
}
