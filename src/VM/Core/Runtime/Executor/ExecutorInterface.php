<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Option;

interface ExecutorInterface
{
    public static function createEntryPoint(KernelInterface $kernel, Option $option): ExecutorInterface;

    public function execute(): ExecutedResult;

    public function context(): ContextInterface;

    public function createContext(?ContextInterface $parentContext = null): ContextInterface;
}
