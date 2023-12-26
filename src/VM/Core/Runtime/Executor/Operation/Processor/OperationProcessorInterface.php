<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation\Processor;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\InsnInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

interface OperationProcessorInterface
{
    public function prepare(InsnInterface $insn, ContextInterface $context): void;

    public function before(): void;

    public function after(): void;

    public function process(): ProcessedStatus;
}
