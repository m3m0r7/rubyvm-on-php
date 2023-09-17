<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Insn\Insn;

trait BreakpointExecutable
{
    protected ?bool $shouldBreakPoint = null;

    public function enableBreakpoint(bool $enabled = true): self
    {
        $this->shouldBreakPoint = $enabled;

        // Renew context
        $this->context = $this->createContext($this->context);

        return $this;
    }

    private function processBreakPoint(Insn $insn, ContextInterface $previousContext, ContextInterface $nextContext): void
    {
        printf('Enter to next step (y/n/q): ');
        $entered = fread(STDIN, 1024);
        $command = strtolower(trim($entered));
        if ('' === $command || 'y' === $command) {
            $this->debugger->showExecutedOperations();
            printf(
                "Current INSN: %s(0x%02x)\n",
                strtolower($insn->name),
                $insn->value,
            );
            printf(
                "Previous Stacks: %s#%d\n",
                (string) $previousContext->vmStack(),
                spl_object_id($previousContext->vmStack()),
            );
            printf(
                "Previous Local Tables: %s\n",
                (string) $previousContext->environmentTable(),
            );
            printf(
                "Current Stacks: %s#%d\n",
                (string) $nextContext->vmStack(),
                spl_object_id($nextContext->vmStack()),
            );
            printf(
                "Current Local Tables: %s\n",
                (string) $nextContext->environmentTable(),
            );
        }
        printf("\n");
        if ('exit' === $command || 'quit' === $command || 'q' === $command) {
            echo "Finished executor, Goodbye ✋\n";

            exit(0);
        }
    }
}
