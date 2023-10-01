<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Debugger;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;

class NullExecutorDebugger implements DebuggerInterface
{
    public function enter(ContextInterface $context): void {}

    public function leave(ExecutedResult $result): void {}

    public function append(Insn $insn, ContextInterface $context): void {}

    public function showExecutedOperations(): void {}

    public function process(Insn $insn, ContextInterface $context): void {}
}
