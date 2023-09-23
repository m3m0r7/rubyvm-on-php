<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;

interface OperationProcessorInterface
{
    public function prepare(Insn $insn, ContextInterface $context): void;

    public function before(): void;

    public function after(): void;

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus;
}
