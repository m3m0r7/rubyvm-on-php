<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\IOContext;
use RubyVM\VM\Core\Runtime\Executor\Context\OperationProcessorContext;
use RubyVM\VM\Core\Runtime\Executor\Context\ProgramCounter;
use RubyVM\VM\Core\Runtime\Executor\Context\VMStack;
use RubyVM\VM\Core\Runtime\Executor\Debugger\BreakpointExecutable;
use RubyVM\VM\Core\Runtime\Executor\Debugger\ExecutorDebugger;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operation;
use RubyVM\VM\Core\Runtime\Main;
use RubyVM\VM\Core\Runtime\OptionInterface;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\HeapSpace\DefaultInstanceHeapSpace;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\YARV\Essential\Symbol\VoidSymbol;
use RubyVM\VM\Exception\ExecutorExeption;
use RubyVM\VM\Exception\ExecutorFailedException;
use RubyVM\VM\Exception\ExecutorUnknownException;
use RubyVM\VM\Exception\RubyVMException;

class Executor implements ExecutorInterface
{
    use BreakpointExecutable;

    protected array $operations = [];
    protected ?bool $shouldProcessedRecords = null;

    protected ContextInterface $context;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly RubyClassInterface $rubyClass,
        private readonly InstructionSequence $instructionSequence,
        private readonly OptionInterface $option,
        private readonly ExecutorDebugger $debugger = new ExecutorDebugger(),
        private readonly ?ContextInterface $previousContext = null,
    ) {
        $this->context = $this->createContext($this->previousContext);
    }

    public function context(): ContextInterface
    {
        return $this->context;
    }

    public static function createEntryPoint(KernelInterface $kernel, OptionInterface $option): ExecutorInterface
    {
        $aux = new Aux(
            loader: new AuxLoader(
                index: $option->entryPointIndex(),
            ),
        );

        $instructionSequence = $kernel->loadInstructionSequence($aux);

        $executor = new Executor(
            $kernel,
            (new Main())->setUserlandHeapSpace(
                $kernel->userlandHeapSpace(),
            ),
            $instructionSequence,
            $option,
        );

        $executor->context()->appendTrace('<main>');

        return $executor;
    }

    public function createContext(?ContextInterface $previousContext = null): ContextInterface
    {
        return new OperationProcessorContext(
            $this->kernel,
            $this,
            $this->rubyClass,
            $previousContext?->vmStack() ?? new VMStack(),
            new ProgramCounter(),
            $this->instructionSequence,
            $this->option,
            $previousContext?->IOContext() ?? new IOContext(
                $this->option->stdOut(),
                $this->option->stdOut(),
                $this->option->stdOut(),
            ),
            $previousContext?->environmentTable() ?? new EnvironmentTable(),
            $this->debugger,
            $previousContext
                ? $previousContext->depth() + 1
                : 0,
            $previousContext?->startTime() ?? null,
            $this->shouldProcessedRecords ?? $previousContext?->shouldProcessedRecords() ?? false,
            $this->shouldBreakPoint ?? $previousContext?->shouldBreakPoint() ?? false,
            $previousContext?->traces() ?? [],
        );
    }

    public function execute(...$arguments): ExecutedResult
    {
        try {
            $result = $this->_execute(...$arguments);

            return new ExecutedResult(
                executor: $this,
                executedStatus: $result->executedStatus,
                returnValue: $result->returnValue,
                threw: null,
                debugger: $this->debugger,
            );
        } catch (RubyVMException $e) {
            return new ExecutedResult(
                executor: $this,
                executedStatus: ExecutedStatus::THREW_EXCEPTION,
                returnValue: null,
                threw: $e,
                debugger: $this->debugger,
            );
        }
    }

    private function _execute(...$arguments): ExecutedResult
    {
        $this->debugger->bindContext($this->context);

        // NOTE: Exceeded counter increments self value including ProcessedStatus::SUCCESS and ProcessedStatus::JUMPED
        // requires this value because a role is outside of the program counter.
        if ($this->context->depth() > ($this->option)::MAX_STACK_EXCEEDED) {
            throw new ExecutorExeption('The executor got max stack exceeded - maybe falling into infinity loop at an executor');
        }

        if (!$this->context->shouldBreakPoint() && $this->context->elapsedTime() > ($this->option)::MAX_TIME_EXCEEDED) {
            throw new ExecutorExeption(sprintf('The executor got max time exceeded %d sec. The process is very heavy or detected an infinity loop', ($this->option)::MAX_TIME_EXCEEDED));
        }

        $operations = $this->instructionSequence
            ->body()
            ->operationEntries;

        $isFinished = false;

        $this->option->logger->info(
            sprintf('Start an executor (total program counter: %d)', count($operations)),
        );

        $infinityLoopCounter = 0;

        for (; $this->context->programCounter()->pos() < count($operations) && !$isFinished; $this->context->programCounter()->increase()) {
            if ($this->context->programCounter()->pos() === $this->context->programCounter()->previousPos()) {
                ++$infinityLoopCounter;
                if ($infinityLoopCounter >= ($this->option)::DETECT_INFINITY_LOOP) {
                    throw new ExecutorExeption('The executor detected infinity loop because the program counter not changes internal counter - you should review incorrect implementation');
                }
            } else {
                $infinityLoopCounter = 0;
            }

            /**
             * @var mixed|Operation $operator
             */
            $operator = $operations[$this->context->programCounter()->pos()] ?? null;
            if (!$operator instanceof Operation) {
                throw new ExecutorExeption(sprintf('The operator is not instantiated by Operation (actual: %s) - maybe an operation code processor has bug(s) or incorrect in implementation [%s]', ClassHelper::nameBy($operator), (string) $operations));
            }

            $this->option->logger->info(
                sprintf(
                    'Start to process an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $this->context->programCounter()->pos(),
                ),
            );

            $processor = $this
                ->option
                ->operationProcessorEntries()
                ->get($operator->insn);

            $this->option->logger->info(
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

            $this->option->logger->info(
                sprintf(
                    'Start to process a before method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name),
                    $operator->insn->value,
                    $this->context->programCounter()->pos(),
                ),
            );

            $processor->before();

            $this->option->logger->info(
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
                );
            }

            $status = $processor->process(...$arguments);

            $this->option->logger->info(
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
            if (ProcessedStatus::FINISH === $status) {
                $isFinished = true;

                continue;
            }

            if (ProcessedStatus::JUMPED === $status) {
                continue;
            }

            if (ProcessedStatus::SUCCESS !== $status) {
                $throwClass = ProcessedStatus::FAILED === $status
                    ? ExecutorFailedException::class
                    : ExecutorUnknownException::class;

                throw new $throwClass(sprintf('The `%s` (opcode: 0x%02x) processor returns %s (%d) status code', $operator->insn->name, $operator->insn->value, $status->name, $status->value));
            }
        }

        if (false === $isFinished) {
            $this->option->logger->emergency(
                sprintf('Illegal finish an executor'),
            );

            throw new ExecutorExeption(sprintf('The executor did not finish - maybe did not call the `%s` (0x%02x)', strtolower(Insn::LEAVE->name), Insn::LEAVE->value));
        }

        if (count($operations) !== $this->context->programCounter()->pos()) {
            $this->option->logger->warning(
                sprintf(
                    'Unmatched expected processing operations and the program counter positions (expected operations: %d, actually program counter: %d)',
                    count($operations),
                    $this->context->programCounter()->pos(),
                ),
            );
        }

        $this->option->logger->info(
            sprintf('Success to finish normally an executor'),
        );

        if (count($this->context->vmStack()) >= 1) {
            return new ExecutedResult(
                executor: $this,
                executedStatus: ExecutedStatus::SUCCESS,
                returnValue: $this->context
                    ->vmStack()
                    ->pop()
                    ->operand,
                threw: null,
                debugger: $this->debugger,
            );
        }

        return new ExecutedResult(
            executor: $this,
            executedStatus: ExecutedStatus::SUCCESS,
            returnValue: (new VoidSymbol())
                ->toRubyClass(),
            threw: null,
            debugger: $this->debugger,
        );
    }

    public function enableProcessedRecords(bool $enabled = true): ExecutorInterface
    {
        $this->shouldProcessedRecords = $enabled;

        // Renew context
        $this->context = $this->createContext($this->context);

        return $this;
    }
}
