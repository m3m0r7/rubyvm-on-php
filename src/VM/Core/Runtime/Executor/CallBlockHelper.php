<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Entity\Array_;
use RubyVM\VM\Core\Runtime\Entity\Nil;
use RubyVM\VM\Core\Runtime\Entity\Number;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\VMCallFlagBit;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\ID;
use RubyVM\VM\Exception\OperationProcessorException;

trait CallBlockHelper
{
    private function callSimpleMethod(ContextInterface $context, RubyClassInterface|ContextInterface ...$arguments): ExecutedResult
    {
        // Validate first value is context?
        $calleeContexts = [];
        if (isset($arguments[0]) && $arguments[0] instanceof ContextInterface) {
            $calleeContexts = [array_shift($arguments)];
        }

        $executor = (new Executor(
            kernel: $context->kernel(),
            rubyClass: $context->self(),
            instructionSequence: $context->instructionSequence(),
            option: $context->option(),
            debugger: $context->debugger(),
            previousContext: $context,
        ));

        $executor->context()
            ->renewEnvironmentTable();

        $iseqBodyData = $executor
            ->context()
            ->instructionSequence()
            ->body()
            ->info();

        $localTableSize = $iseqBodyData
            ->localTableSize();

        $size = $iseqBodyData->objectParam()->size();

        $comparedArgumentsSizeByLocalSize = min($size, count($arguments));

        $startArguments = (Option::VM_ENV_DATA_SIZE + $localTableSize) - $comparedArgumentsSizeByLocalSize;

        // NOTE: this var means to required parameter (non optional parameter)
        $paramLead = $iseqBodyData->objectParam()->leadNum();

        // NOTE: Implements splat expression
        if ($iseqBodyData->objectParam()->objectParamFlags()->hasRest()) {
            $restStart = $iseqBodyData->objectParam()->restStart();

            $startOfSplat = count($arguments) - $restStart;

            $arguments = self::alignArguments(
                array_reverse(array_slice($arguments, 0, $startOfSplat)),
                ...array_slice($arguments, $startOfSplat),
            );
        }

        for ($localIndex = 0; $localIndex < $comparedArgumentsSizeByLocalSize; ++$localIndex) {
            $argument = $arguments[$localIndex];
            $slotIndex = LocalTableHelper::computeLocalTableIndex(
                $localTableSize,
                $startArguments + $localIndex,
            );

            $executor->context()
                ->environmentTable()
                ->setWithLead(
                    $slotIndex,
                    $argument,
                    // NOTE: The parameter is coming by reversed
                    $paramLead <= ($size - $localIndex),
                );
        }

        $result = $executor
            ->execute(...$calleeContexts);

        if ($result->threw instanceof \Throwable) {
            throw $result->threw;
        }

        return $result;
    }

    private function callBlockWithArguments(CallInfoInterface $callInfo, Number $blockIseqIndex, RubyClassInterface $blockObject, bool $isSuper, CallInfoInterface|RubyClassInterface|ID|ExecutedResult ...$arguments): ?RubyClassInterface
    {
        // @phpstan-ignore-next-line
        if ($this->context === null) {
            throw new OperationProcessorException('The runtime context is not injected - did you forget to call setRuntimeContext before?');
        }

        if (($callInfo->callData()->flag() & (0x01 << VMCallFlagBit::VM_CALL_ARGS_BLOCKARG->value)) !== 0) {
            throw new OperationProcessorException('The callBlockWithArguments is not implemented yet');
        }

        if ($blockIseqIndex->valueOf() === 0) {
            // TODO: implement a super call
            // see: https://github.com/ruby/ruby/blob/ruby_3_2/vm_args.c#L888

            throw new OperationProcessorException('The callBlockWithArguments is not implemented yet');
        }

        $instructionSequence = $this->context
            ->kernel()
            ->loadInstructionSequence(new Aux(
                loader: new AuxLoader(
                    index: $blockIseqIndex->valueOf(),
                ),
            ));

        $instructionSequence->load();

        $executor = (new Executor(
            kernel: $this->context->kernel(),
            rubyClass: $this->context->self(),
            instructionSequence: $instructionSequence,
            option: $this->context->option(),
            debugger: $this->context->debugger(),
            previousContext: $this->context,
        ));

        $result = $blockObject
            ->setRuntimeContext($executor->context())
            ->setUserlandHeapSpace($executor->context()->self()->userlandHeapSpace())
            ->{(string) $callInfo->callData()->mid()->object}(
                $executor->context(),
                ...$arguments,
            );

        if ($result instanceof ExecutedResult) {
            if ($result->threw instanceof \Throwable) {
                throw $result->threw;
            }

            return $result->returnValue;
        }

        return $result;
    }

    /**
     * @param (array<ContextInterface|RubyClassInterface>[]|ContextInterface|RubyClassInterface)[] ...$arguments
     *
     * @return (array<ContextInterface|RubyClassInterface>[]|ContextInterface|RubyClassInterface)[]
     */
    private static function alignArguments(RubyClassInterface|ContextInterface|array ...$arguments): array
    {
        $newArguments = [];

        foreach ($arguments as $argument) {
            if (is_array($argument)) {
                $newArguments[] = Array_::createBy(
                    array_map(
                        // Extract symbol
                        // Do not expected coming here an array but PHP Stan will show an error
                        // @phpstan-ignore-next-line
                        static function (RubyClassInterface|ContextInterface $rubyClass) {
                            if ($rubyClass instanceof ContextInterface) {
                                return Nil::createBy()
                                    ->symbol();
                            }

                            return $rubyClass
                                ->entity()
                                ->symbol();
                        },
                        $argument,
                    )
                )->toBeRubyClass();

                continue;
            }

            if ($argument instanceof ContextInterface) {
                $newArguments[] = Nil::createBy()
                    ->toBeRubyClass();

                continue;
            }

            $newArguments[] = $argument;
        }

        return $newArguments;
    }
}
