<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Void_;
use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\IOContext;
use RubyVM\VM\Core\Runtime\Executor\Context\NullContext;
use RubyVM\VM\Core\Runtime\Executor\Context\OperationProcessorContext;
use RubyVM\VM\Core\Runtime\Executor\Context\ProgramCounter;
use RubyVM\VM\Core\Runtime\Executor\Context\VMStack;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operation;
use RubyVM\VM\Core\Runtime\Main;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\OptionInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;
use RubyVM\VM\Exception\ExecutorExeption;
use RubyVM\VM\Exception\ExecutorFailedException;
use RubyVM\VM\Exception\ExecutorUnknownException;
use RubyVM\VM\Exception\ExitException;
use RubyVM\VM\Exception\Raise;
use RubyVM\VM\Exception\RubyVMException;

class Executor implements ExecutorInterface
{
    protected ContextInterface $context;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly RubyClassInterface $rubyClass,
        private readonly InstructionSequenceInterface $instructionSequence,
        private readonly OptionInterface $option,
        private readonly ?ContextInterface $parentContext = null,
    ) {
        $this->context = $this->createContext($this->parentContext);
        if (!$this->rubyClass->hasRuntimeContext()) {
            $this->rubyClass->setRuntimeContext($this->context);
        }
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

        $main = new Main();
        $executor = new Executor(
            $kernel,
            $main->setUserlandHeapSpace(
                $kernel->userlandHeapSpace(),
            )->setRuntimeContext(
                new NullContext(
                    $kernel,
                    $main,
                    $option,
                ),
            ),
            $instructionSequence,
            $option,
        );

        $executor->context()->appendTrace('<main>');

        return $executor;
    }

    public function createContext(?ContextInterface $parentContext = null): ContextInterface
    {
        return new OperationProcessorContext(
            $parentContext,
            $this->kernel,
            $this,
            $this->rubyClass,
            $parentContext?->vmStack() ?? new VMStack(),
            new ProgramCounter(),
            $this->instructionSequence,
            $this->option,
            $parentContext?->IOContext() ?? new IOContext(
                $this->option->stdOut(),
                $this->option->stdIn(),
                $this->option->stdErr(),
            ),
            $parentContext?->environmentTable() ?? new EnvironmentTable(),
            $parentContext instanceof \RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface
                ? $parentContext->depth() + 1
                : 0,
            $parentContext?->startTime() ?? null,
            $parentContext?->traces() ?? [],
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
                threw: null,
            );
        } catch (Raise $e) {
            // Write to stderr
            $this->context
                ->IOContext()
                ->stdErr
                ->write((string) $e);

            return new ExecutedResult(
                executor: $this,
                executedStatus: ExecutedStatus::EXIT,
                returnValue: null,
                threw: $e,
            );
        } catch (ExitException $e) {
            // Bubbling to main context
            throw $e;
        } catch (RubyVMException $e) {
            return new ExecutedResult(
                executor: $this,
                executedStatus: ExecutedStatus::THREW_EXCEPTION,
                returnValue: null,
                threw: $e,
            );
        }
    }

    private function _execute(ContextInterface|RubyClassInterface ...$arguments): ExecutedResult
    {
        $this->option->debugger()->enter($this->context);

        // NOTE: Exceeded counter increments self value including ProcessedStatus::SUCCESS and ProcessedStatus::JUMPED
        // requires this value because a role is outside of the program counter.
        if ($this->context->depth() > Option::MAX_STACK_EXCEEDED) {
            throw new ExecutorExeption('The executor got max stack exceeded - maybe falling into infinity loop at an executor');
        }

        if ($this->context->elapsedTime() > Option::MAX_TIME_EXCEEDED) {
            throw new ExecutorExeption(sprintf('The executor got max time exceeded %d sec. The process is very heavy or detected an infinity loop', Option::MAX_TIME_EXCEEDED));
        }

        $operations = $this->instructionSequence
            ->body()
            ->info()
            ->operationEntries();

        $isFinished = false;

        $this->option->logger()->info(
            sprintf('Start an executor (total program counter: %d)', count($operations)),
        );

        $infinityLoopCounter = 0;

        for (; $this->context->programCounter()->pos() < count($operations) && !$isFinished; $this->context->programCounter()->increase()) {
            if ($this->context->programCounter()->pos() === $this->context->programCounter()->previousPos()) {
                ++$infinityLoopCounter;
                if ($infinityLoopCounter >= Option::DETECT_INFINITY_LOOP) {
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

            $this->option->logger()->info(
                sprintf(
                    'Start to process an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name()),
                    $operator->insn->value(),
                    $this->context->programCounter()->pos(),
                ),
            );

            $processor = $this
                ->kernel
                ->operationProcessorEntries()
                ->get($operator->insn);

            $this->option->logger()->info(
                sprintf(
                    'Start to prepare an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name()),
                    $operator->insn->value(),
                    $this->context->programCounter()->pos(),
                ),
            );

            $startTime = microtime(true);
            $snapshotContext = $this->context->createSnapshot();

            $processor->prepare(
                $operator->insn,
                $this->context,
            );

            $this->option->logger()->info(
                sprintf(
                    'Start to process a before method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name()),
                    $operator->insn->value(),
                    $this->context->programCounter()->pos(),
                ),
            );

            $processor->before();

            $this->option->logger()->info(
                sprintf(
                    'Start to process a main routine method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name()),
                    $operator->insn->value(),
                    $this->context->programCounter()->pos(),
                ),
            );

            $details = null;

            $this->option->debugger()
                ->append(
                    $operator->insn,
                    $snapshotContext,
                );

            $status = $processor->process();

            $this->option->logger()->info(
                sprintf(
                    'Start to process a post method an INSN `%s` (0x%02x) (ProgramCounter: %d)',
                    strtolower($operator->insn->name()),
                    $operator->insn->value(),
                    $this->context->programCounter()->pos(),
                ),
            );

            $processor->after();

            $this->option->debugger()
                ->process(
                    $operator->insn,
                    $snapshotContext,
                );

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

                throw new $throwClass(sprintf('The `%s` (opcode: 0x%02x) processor returns %s (%d) status code', $operator->insn->name(), $operator->insn->value(), $status->name, $status->value));
            }
        }

        if (false === $isFinished) {
            $this->option->logger()->emergency(
                'Illegal finish an executor',
            );

            throw new ExecutorExeption('The executor did not finish - maybe did not call the `leave` opcode');
        }

        if (count($operations) !== $this->context->programCounter()->pos()) {
            $this->option->logger()->warning(
                sprintf(
                    'Unmatched expected processing operations and the program counter positions (expected operations: %d, actually program counter: %d)',
                    count($operations),
                    $this->context->programCounter()->pos(),
                ),
            );
        }

        $this->option->logger()->info(
            'Success to finish normally an executor',
        );

        $returnValue = Void_::createBy();
        if (count($this->context->vmStack()) >= 1) {
            $returnValue = $this->context
                ->vmStack()
                ->pop()
                ->operand;
            if ($returnValue instanceof ExecutedResult) {
                if ($returnValue->threw instanceof \Throwable) {
                    throw $returnValue->threw;
                }

                $returnValue = $returnValue->returnValue;
            }

            if (!$returnValue instanceof RubyClassInterface) {
                throw new ExecutorExeption(
                    sprintf(
                        'The operand is not instantiated by RubyClassInterface: %s',
                        ClassHelper::nameBy($returnValue),
                    ),
                );
            }
        }

        $result = new ExecutedResult(
            executor: $this,
            executedStatus: ExecutedStatus::SUCCESS,
            returnValue: $returnValue,
            threw: null,
        );

        $this->option->debugger()->leave($result);

        return $result;
    }
}
