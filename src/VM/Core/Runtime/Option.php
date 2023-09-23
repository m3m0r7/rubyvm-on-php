<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;
use RubyVM\VM\Stream\StreamHandler;
use RubyVM\VM\Stream\StreamHandlerInterface;

class Option implements OptionInterface
{
    public const DETECT_INFINITY_LOOP = 3;
    public const MAX_TIME_EXCEEDED = 5;
    public const MAX_STACK_EXCEEDED = 30;
    public const RUBY_ENCINDEX_BUILTIN_MAX = 12;
    public const RSV_TABLE_INDEX_0 = 0;
    public const RSV_TABLE_INDEX_1 = 1;

    public const VM_ENV_DATA_SIZE = 3;

    public const DEFAULT_ENTRYPOINT_AUX_INDEX = 0;

    public function __construct(
        public readonly BinaryStreamReaderInterface $reader,
        public readonly LoggerInterface $logger,
        private ?StreamHandlerInterface $stdOut = null,
        private ?StreamHandlerInterface $stdIn = null,
        private ?StreamHandlerInterface $stdErr = null,
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
}
