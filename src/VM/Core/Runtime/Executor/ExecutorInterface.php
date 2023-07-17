<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

interface ExecutorInterface
{
    public function execute(
        VMStack $vmStack = new VMStack(),
    ): ExecutedStatus;

    public function createContext(
        VMStack $vmStack = new VMStack(),
        ProgramCounter $pc = new ProgramCounter(),
    ): OperationProcessorContext;

    public function debugger(): ExecutorDebugger;
}
