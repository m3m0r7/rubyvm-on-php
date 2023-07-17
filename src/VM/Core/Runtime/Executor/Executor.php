<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\VoidSymbol;
use RubyVM\VM\Exception\ExecutorExeption;
use RubyVM\VM\Exception\ExecutorFailedException;
use RubyVM\VM\Exception\ExecutorUnknownException;

class Executor implements ExecutorInterface
{
    private const RSV_LOCAL_TABLE_0 = 0;
    private const RSV_LOCAL_TABLE_1 = 1;
    private const RSV_LOCAL_TABLE_2 = 2;

    protected array $operations = [];

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly MainInterface $main,
        private readonly OperationProcessorEntries $operationProcessorEntries,
        private readonly InstructionSequence $instructionSequence,
        private readonly LoggerInterface $logger,
        private readonly EnvironmentTableEntries $environmentTableEntries,
        private readonly ExecutorDebugger $debugger = new ExecutorDebugger(),
    ) {
    }

    public function execute(VMStack $vmStack = new VMStack()): ExecutedResult
    {
        try {
            $result = $this->_execute($vmStack);
            return new ExecutedResult(
                executor: $this,
                executedStatus: $result->executedStatus,
                returnValue: $result->returnValue,
                throwed: null,
                debugger: $this->debugger,
            );
        } catch (\Throwable $e) {
            return new ExecutedResult(
                executor: $this,
                executedStatus: ExecutedStatus::IN_COMPLETED,
                returnValue: null,
                throwed: $e,
                debugger: $this->debugger,
            );
        }
    }

    private function _execute(VMStack $vmStack = new VMStack()): ExecutedResult
    {
        $operations = $this->instructionSequence->operations();
        $pc = new ProgramCounter();

        $isFinished = false;

        $this->logger->info(
            sprintf('Start an executor (total program counter: %d)', count($operations)),
        );

        $infinityLoopCounter = 0;

        // NOTE: Exceeded counter increments self value including ProcessedStatus::SUCCESS and ProcessedStatus::JUMPED
        // requires this value because a role is outside of the program counter.
        $exceededCounter = 0;

        for (;$pc->pos() < count($operations) && !$isFinished; ++$exceededCounter, $pc->increase()) {
            if ($exceededCounter > Option::MAX_STACK_EXCEEDED) {
                throw new ExecutorExeption(
                    'The executor got max stack exceeded - maybe falling into infinity loop at an executor'
                );
            }
            if ($pc->pos() === $pc->previousPos()) {
                $infinityLoopCounter++;
                if ($infinityLoopCounter >= Option::DETECT_INFINITY_LOOP) {
                    throw new ExecutorExeption(
                        'The executor detected infinity loop because the program counter not changes internal counter - you should review incorrect implementation'
                    );
                }
            } else {
                $infinityLoopCounter = 0;
            }

            /**
             * @var OperationEntry|mixed $operator
             */
            $operator = $operations[$pc->pos()] ?? null;
            if (!($operator instanceof OperationEntry)) {
                throw new ExecutorExeption(
                    sprintf(
                        'The operator is not instantiated by OperationEntry (actual: %s) - maybe an operation code processor has bug(s) or incorrect in implementation',
                        is_object($operator)
                            ? ClassHelper::nameBy($operator)
                            : gettype($operator),
                    )
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

            $context = $this->createContext(
                $vmStack,
                $pc,
            );

            $startTime = microtime(true);
            $snapshotContext = $context->createSnapshot();

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

            $this->debugger->append(
                $operator->insn,
                (int) (microtime(true) - $startTime),
                $snapshotContext,
            );

            // Finish this loop when returning ProcessedStatus::FINISH
            if ($status === ProcessedStatus::FINISH) {
                $isFinished = true;
                continue;
            }

            if ($status === ProcessedStatus::JUMPED) {
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

        if (count($vmStack) > 1) {
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

        if (count($vmStack) === 1) {
            return new ExecutedResult(
                executor: $this,
                executedStatus: ExecutedStatus::SUCCESS,
                returnValue: $vmStack->pop()
                    ->operand
                    ->symbol,
                throwed: null,
                debugger: $this->debugger,
            );
        }

        return new ExecutedResult(
            executor: $this,
            executedStatus: ExecutedStatus::SUCCESS,
            returnValue: new VoidSymbol(),
            throwed: null,
            debugger: $this->debugger,
        );
    }

    public function createContext(
        VMStack $vmStack = new VMStack(),
        ProgramCounter $pc = new ProgramCounter(),
    ): OperationProcessorContext {
        return new OperationProcessorContext(
            $this->kernel,
            $this,
            $this->main,
            $vmStack,
            $pc,
            $this->operationProcessorEntries,
            $this->instructionSequence,
            $this->logger,
            $this->environmentTableEntries,
            $this->debugger,
        );
    }
}
