<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Exception\ExecutorExeption;

class Executor implements ExecutorInterface
{
    protected array $operations = [];

    public function __construct(
        private readonly OperationProcessorEntries $operationProcessorEntries,
        public readonly InstructionSequence        $instructionSequence,
        private readonly LoggerInterface           $logger,
    ) {
    }

    public function execute(): ExecutedStatus
    {
        $operations = $this->instructionSequence->operations();
        $vmStack = new VMStack();
        $pc = new ProgramCounter();

        for (; $pc->pos() < count($operations); $pc->increase()) {
            /**
             * @var OperationEntry|mixed $operator
             */
            $operator = $operations[$pc->pos()] ?? null;
            if (!($operator instanceof OperationEntry)) {
                throw new ExecutorExeption(
                    'The operator is not instantiated by OperationEntry - maybe an operation code processor has bug(s) or incorrect in implementation'
                );
            }

            $processor = $this
                ->operationProcessorEntries
                ->get($operator->insn);

            $processor->prepare(
                $operator->insn,
                $pc,
                $vmStack,
                $this->logger,
            );
            $processor->before();
            $status = $processor->process();
            $processor->after();

            if ($status !== ProcessedStatus::SUCCESS) {
                throw new ExecutorExeption(
                    sprintf(
                        'The `%s` (opcode: 0x%02x) processor returns %s (%d) status code',
                        $operator->insn->name,
                        $operator->insn->value,
                        $status->name,
                        $status->value,
                    ),
                );
            }
        }

        return ExecutedStatus::SUCCESS;
    }

}
