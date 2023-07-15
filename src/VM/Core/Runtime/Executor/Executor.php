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

    public function __construct(
        private readonly MainInterface $main,
        private readonly OperationProcessorEntries $operationProcessorEntries,
        private readonly InstructionSequence $instructionSequence,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(): ExecutedStatus
    {
        $operations = $this->instructionSequence->operations();
        $vmStack = new VMStack();
        $pc = new ProgramCounter();

        $isFinished = false;

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
                new OperationProcessorContext(
                    $this->main,
                    $vmStack,
                    $pc,
                    $this->instructionSequence,
                    $this->logger,
                ),
            );
            $processor->before();
            $status = $processor->process();
            $processor->after();

            // Finish this loop when returning ProcessedStatus::FINISH
            if ($status === ProcessedStatus::FINISH) {
                $isFinished = true;
                break;
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
            throw new ExecutorExeption(
                'The executor did not finish - maybe did not call the `%s` (0x%02x)',
                Insn::LEAVE->name,
                Insn::LEAVE->value,
            );
        }

        return ExecutedStatus::SUCCESS;
    }
}
