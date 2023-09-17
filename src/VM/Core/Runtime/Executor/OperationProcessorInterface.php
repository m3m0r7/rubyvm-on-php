<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

interface OperationProcessorInterface
{
    public function prepare(Insn $insn, ContextInterface $context): void;

    public function before(): void;

    public function after(): void;

    public function process(SymbolInterface|ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus;
}
