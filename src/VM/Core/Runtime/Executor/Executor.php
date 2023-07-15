<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Exception\ExecutorExeption;
use RubyVM\VM\Exception\ExecutorFailedException;
use RubyVM\VM\Exception\ExecutorUnknownException;

class Executor implements ExecutorInterface
{
    protected array $operations = [];
    protected readonly ExecutorDebugger $executorDebugger;

    public function __construct(
        private readonly MainInterface $main,
        private readonly OperationProcessorEntries $operationProcessorEntries,
        private readonly InstructionSequence $instructionSequence,
        private readonly LoggerInterface $logger,
    ) {
        $this->executorDebugger = new ExecutorDebugger();
    }

    public function execute(): ExecutedStatus
    {
        $operations = $this->instructionSequence->operations();
        $vmStack = new VMStack();
        $pc = new ProgramCounter();

        $isFinished = false;

        $this->logger->info(
            sprintf('Start an executor (total program counter: %d)', count($operations)),
        );

        for (; $pc->pos() < count($operations) && !$isFinished; $pc->increase()) {
            /**
             * @var OperationEntry|mixed $operator
             */
            $operator = $operations[$pc->pos()] ?? null;
            if (!($operator instanceof OperationEntry)) {
                throw new ExecutorExeption(
                    'The operator is not instantiated by OperationEntry - maybe an operation code processor has bug(s) or incorrect in implementation'
                );
            }


            $this->logger->info(
                sprintf(
                    'Start to process an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $pc->pos(),
                ),
            );

            $processor = $this
                ->operationProcessorEntries
                ->get($operator->insn);

            $this->logger->info(
                sprintf(
                    'Start to prepare an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $pc->pos(),
                ),
            );

            $context = new OperationProcessorContext(
                $this->main,
                $vmStack,
                $pc,
                $this->instructionSequence,
                $this->logger,
            );

            $startTime = microtime(true);
            $snapshotContext = clone $context;

            $processor->prepare(
                $operator->insn,
                $context,
            );

            $this->logger->info(
                sprintf(
                    'Start to process a before method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $pc->pos(),
                ),
            );

            $processor->before();

            $this->logger->info(
                sprintf(
                    'Start to process a main routine method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $pc->pos(),
                ),
            );

            $status = $processor->process();

            $this->logger->info(
                sprintf(
                    'Start to process a post method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $pc->pos(),
                ),
            );

            $processor->after();

            $this->executorDebugger->append(
                $operator->insn,
                (int) (microtime(true) - $startTime),
                $snapshotContext,
            );

            // Finish this loop when returning ProcessedStatus::FINISH
            if ($status === ProcessedStatus::FINISH) {
                $isFinished = true;
                continue;
            }

            if ($status !== ProcessedStatus::SUCCESS) {
                $throwClass = $status === ProcessedStatus::FAILED
                    ? ExecutorFailedException::class
                    : ExecutorUnknownException::class;

                throw new $throwClass(
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

        if ($isFinished === false) {
            $this->logger->emergency(
                sprintf('Illegal finish an executor'),
            );

            throw new ExecutorExeption(
                sprintf(
                    'The executor did not finish - maybe did not call the `%s` (0x%02x)',
                    strtolower(Insn::LEAVE->name),
                    Insn::LEAVE->value,
                ),
            );
        }

        if (count($vmStack) > 0) {
            $this->logger->warning(
                sprintf(
                    'The VM stack has more remained stacked (remaining: %d) which cause memory leak in the near future',
                    count($vmStack),
                ),
            );
        }

        if (count($operations) !== $pc->pos()) {
            $this->logger->warning(
                sprintf(
                    'Unmatched expected processing operations and the program counter positions (expected operations: %d, actually program counter: %d)',
                    count($operations),
                    $pc->pos(),
                ),
            );
        }

        $this->logger->info(
            sprintf('Success to finish normally an executor'),
        );

        return ExecutedStatus::SUCCESS;
    }

    public function debugger(): ExecutorDebugger
    {
        return $this->executorDebugger;
    }
}
