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
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
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

    protected bool $shouldBreakPoint = false;
    protected bool $shouldProcessedRecords = false;

    protected ContextInterface $context;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly MainInterface $main,
        private readonly OperationProcessorEntries $operationProcessorEntries,
        private readonly InstructionSequence $instructionSequence,
        private readonly LoggerInterface $logger,
        private readonly ExecutorDebugger $debugger = new ExecutorDebugger(),
        private readonly ?ContextInterface $previousContext = null
    ) {
        $this->context = $this->createContext($previousContext);
    }

    public function context(): ContextInterface
    {
        return $this->context;
    }

    public function createContext(?ContextInterface $previousContext = null): ContextInterface
    {
        return new OperationProcessorContext(
            $this->kernel,
            $this,
            $this->main,
            $previousContext?->vmStack() ?? new VMStack(),
            new ProgramCounter(),
            $this->operationProcessorEntries,
            $this->instructionSequence,
            $this->logger,
            new EnvironmentTableEntries(),
            $this->debugger,
            $previousContext
                ? $previousContext->depth() + 1
                : 0,
            $previousContext?->startTime() ?? null,
            $previousContext?->shouldProcessedRecords() ?? $this->shouldProcessedRecords,
            $previousContext?->shouldBreakPoint() ?? $this->shouldBreakPoint,
        );
    }

    public function execute(): ExecutedResult
    {
        try {
            $result = $this->_execute();
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

    private function _execute(): ExecutedResult
    {
        $this->debugger->bindContext($this->context);

        // NOTE: Exceeded counter increments self value including ProcessedStatus::SUCCESS and ProcessedStatus::JUMPED
        // requires this value because a role is outside of the program counter.
        if ($this->context->depth() > Option::MAX_STACK_EXCEEDED) {
            throw new ExecutorExeption(
                'The executor got max stack exceeded - maybe falling into infinity loop at an executor'
            );
        }

        if (!$this->context->shouldBreakPoint() && $this->context->elapsedTime() > Option::MAX_TIME_EXCEEDED) {
            throw new ExecutorExeption(
                sprintf(
                    'The executor got max time exceeded %d sec. The process is very heavy or detected an infinity loop',
                    Option::MAX_TIME_EXCEEDED,
                ),
            );
        }

        $operations = $this->instructionSequence->operations();

        echo $operations;

        $isFinished = false;

        $this->logger->info(
            sprintf('Start an executor (total program counter: %d)', count($operations)),
        );

        $infinityLoopCounter = 0;

        for (;$this->context->programCounter()->pos() < count($operations) && !$isFinished; $this->context->programCounter()->increase()) {
            if ($this->context->programCounter()->pos() === $this->context->programCounter()->previousPos()) {
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
            $operator = $operations[$this->context->programCounter()->pos()] ?? null;
            if (!($operator instanceof OperationEntry)) {
                throw new ExecutorExeption(
                    sprintf(
                        'The operator is not instantiated by OperationEntry (actual: %s) - maybe an operation code processor has bug(s) or incorrect in implementation [%s]',
                        is_object($operator)
                            ? ClassHelper::nameBy($operator)
                            : gettype($operator),
                        (string) $operations,
                    )
                );
            }

            $this->logger->info(
                sprintf(
                    'Start to process an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $this->context->programCounter()->pos(),
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
                    $this->context->programCounter()->pos(),
                ),
            );

            $startTime = microtime(true);
            $snapshotContext = $this->context->createSnapshot();

            $processor->prepare(
                $operator->insn,
                $this->context,
            );

            $this->logger->info(
                sprintf(
                    'Start to process a before method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $this->context->programCounter()->pos(),
                ),
            );

            $processor->before();

            $this->logger->info(
                sprintf(
                    'Start to process a main routine method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $this->context->programCounter()->pos(),
                ),
            );

            $details = null;

            if ($this->context->shouldProcessedRecords()) {
                $this->debugger->append(
                    $operator->insn,
                    $snapshotContext,
                    $this->makeDetails($operator->insn),
                );
            }

            $status = $processor->process();

            $this->logger->info(
                sprintf(
                    'Start to process a post method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $this->context->programCounter()->pos(),
                ),
            );

            $processor->after();

            if ($this->context->shouldBreakPoint()) {
                $this->processBreakPoint(
                    $operator->insn,
                    $snapshotContext,
                    $this->context,
                );
            }

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

        if (count($operations) !== $this->context->programCounter()->pos()) {
            $this->logger->warning(
                sprintf(
                    'Unmatched expected processing operations and the program counter positions (expected operations: %d, actually program counter: %d)',
                    count($operations),
                    $this->context->programCounter()->pos(),
                ),
            );
        }

        $this->logger->info(
            sprintf('Success to finish normally an executor'),
        );

        if (count($this->context->vmStack()) >= 1) {
            return new ExecutedResult(
                executor: $this,
                executedStatus: ExecutedStatus::SUCCESS,
                returnValue: $this->context
                    ->vmStack()
                    ->pop()
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

    public function enableBreakpoint(bool $enabled = true): self
    {
        $this->shouldBreakPoint = $enabled;

        // Renew context
        $this->context = $this->createContext($this->previousContext);
        return $this;
    }

    private function processBreakPoint(Insn $insn, ContextInterface $previousContext, ContextInterface $nextContext): void
    {
        printf('Enter to next step (y/n/q): ');
        $entered = fread(STDIN, 1024);
        $command = strtolower(trim($entered));
        if ($command === '' || $command === 'y') {
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
                (string) $previousContext->environmentTableEntries(),
            );
            printf(
                "Current Stacks: %s#%d\n",
                (string) $nextContext->vmStack(),
                spl_object_id($nextContext->vmStack()),
            );
            printf(
                "Current Local Tables: %s\n",
                (string) $nextContext->environmentTableEntries(),
            );
        }
        printf("\n");
        if ($command === 'exit' || $command === 'quit' || $command === 'q') {
            echo "Finished executor, Goodbye ✋\n";
            exit(0);
        }
    }

    private function makeDetails(Insn $insn): ?string
    {
        $context = $this->context->createSnapshot();
        if ($insn === Insn::OPT_SEND_WITHOUT_BLOCK) {
            $details = '';
            $currentPos = $context->programCounter()->pos();
            $vmStack = clone $context->vmStack();

            /**
             * @var OperandEntry $callDataOperand
             */
            $callDataOperand = $context
                ->instructionSequence()
                ->operations()
                ->get($currentPos + 1);

            $arguments = [];
            for ($i = 0; $i < $callDataOperand->operand->callData()->argumentsCount(); $i++) {
                $arguments[] = $vmStack->pop();
            }

            /**
             * @var OperandEntry|MainInterface $class
             */
            $class = $vmStack->pop();

            $context->programCounter()->set($currentPos);
            return sprintf(
                '%s#%s(%s)',
                ClassHelper::nameBy($class->operand),
                (string) $callDataOperand
                    ->operand
                    ->callData()
                    ->mid()
                    ->object
                    ->symbol,
                implode(
                    ', ',
                    array_map(
                        fn ($argument) => match ($argument::class) {
                            SymbolInterface::class => (string) $argument,
                            OperandEntry::class => (string) $argument->operand->symbol,
                            default => '?',
                        },
                        $arguments,
                    ),
                ),
            );
        }
        return null;
    }

    public function enableProcessedRecords(bool $enabled = true): ExecutorInterface
    {
        $this->shouldProcessedRecords = $enabled;

        // Renew context
        $this->context = $this->createContext($this->previousContext);
        return $this;
    }
}
