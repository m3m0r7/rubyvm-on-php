<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Verification\Verifier;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;

interface KernelInterface
{
    public function setup(): KernelInterface;
    public function process(): ExecutorInterface;

    /**
     * @return RubyVersion[]
     */
    public function expectedVersions(): array;
    public function stream(): BinaryStreamReaderInterface;

    public function findId(int $index): ID;
    public function findObject(int $index): Object_;
}
