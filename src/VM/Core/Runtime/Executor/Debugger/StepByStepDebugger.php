<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Debugger;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;

class StepByStepDebugger extends DefaultExecutorDebugger implements DebuggerInterface
{
    private function processBreakPoint(Insn $insn, ContextInterface $prevContext, ContextInterface $nextContext): void
    {
        $this->showExecutedOperations();

        printf(
            "Current INSN: %s(0x%02x)\n",
            strtolower($insn->name),
            $insn->value,
        );
        printf(
            "Previous Stacks: %s#%d\n",
            (string) $prevContext->vmStack(),
            spl_object_id($prevContext->vmStack()),
        );
        printf(
            "Previous Local Tables: %s\n",
            (string) $prevContext->environmentTable(),
        );
        printf(
            "Current Stacks: %s&#%d\n",
            (string) $nextContext->vmStack(),
            spl_object_id($nextContext->vmStack()),
        );
        printf(
            "Current Local Tables: %s\n",
            (string) $nextContext->environmentTable(),
        );

        printf("\n");
        printf('Enter to next step (y/q): ');

        $entered = fread(STDIN, 1024);
        if ($entered === false) {
            echo "The stream cannot read 😭 may be closed stream pointer\n";

            exit(1);
        }

        $command = strtolower(trim($entered));

        if ('exit' === $command || 'quit' === $command || 'q' === $command) {
            echo "Finished executor, Goodbye ✋\n";

            exit(0);
        }
    }

    public function enter(ContextInterface $context): void
    {
        $this->context = $context->createSnapshot();
    }

    public function process(Insn $insn, ContextInterface $context): void
    {
        $this->processBreakPoint(
            $insn,
            $this->context,
            $context->createSnapshot(),
        );
    }
}
