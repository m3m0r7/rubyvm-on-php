<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\Insn\Insn;

interface OperationProcessorInterface
{
    public function prepare(Insn $insn, ProgramCounter $pc, VMStack $VMStack, LoggerInterface $logger): void;
    public function before(): void;
    public function after(): void;
    public function process(): ProcessedStatus;
}
