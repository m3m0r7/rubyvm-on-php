<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Attribute\WithContext;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Class_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Array_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\VMCallFlagBit;
use RubyVM\VM\Core\YARV\Criterion\Entry\Variable;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Exception\OperationProcessorException;

trait CallBlockHelper
{
    public function send(string $name, CallInfoInterface $callInfo, ?ExecutorInterface $block, RubyClassInterface ...$arguments): ExecutedResult|RubyClassInterface
    {
        if (method_exists($this, $name)) {
            $reflection = new \ReflectionClass($this);
            $method = $reflection->getMethod($name);

            $hasWithContextAttr = $method->getAttributes(WithContext::class) !== [];

            if ($hasWithContextAttr) {
                $arguments = [
                    // Append block context
                    $block?->context(),
                    ...$arguments,
                ];
            }

            $result = $this->{$name}(...$arguments);

            if (is_int($result)) {
                return Integer_::createBy($result);
            }

            if (is_string($result)) {
                return String_::createBy($result);
            }

            return $result;
        }

        return $this->{$name}(
            $callInfo,
            $block,
            ...$arguments,
        );
    }

    private function callSimpleMethod(ContextInterface $context, CallInfoInterface $callInfo, ?ExecutorInterface $block, RubyClassInterface ...$arguments): ExecutedResult
    {
        if (($callInfo->callData()->flag() & (0x01 << VMCallFlagBit::VM_CALL_ARGS_BLOCKARG->value)) !== 0) {
            throw new OperationProcessorException('The callBlockWithArguments is not implemented yet');
        }

        $isSameClass = $this instanceof RubyClassInterface
            && $context->self() instanceof Class_
            && $this->className() === $context->self()->valueOf();

        $executor = (new Executor(
            kernel: $context->kernel(),
            // Use runtime class if is the same class constant and current class
            rubyClass: $isSameClass
                ? $this
                : $context->self(),
            instructionSequence: $context->instructionSequence(),
            option: $context->option(),
            parentContext: $context,
        ));

        $executor->context()
            ->renewEnvironmentTable();

        if ($block instanceof \RubyVM\VM\Core\Runtime\Executor\ExecutorInterface) {
            $executor->context()
                ->environmentTable()
                ->set(
                    Option::VM_ENV_DATA_INDEX_SPECVAL,
                    $block->context(),
                );
        }

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
                $callInfo,
                $executor->context(),
                array_reverse(array_slice($arguments, 0, $startOfSplat)),
                ...array_slice($arguments, $startOfSplat),
            );
        } else {
            $arguments = self::alignArguments(
                $callInfo,
                $executor->context(),
                ...$arguments,
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
            ->execute();

        if ($result->threw instanceof \Throwable) {
            throw $result->threw;
        }

        return $result;
    }

    /**
     * @param (ContextInterface|RubyClassInterface)[] ...$arguments
     *
     * @return (ContextInterface|RubyClassInterface)[]
     */
    private static function alignArguments(?CallInfoInterface $callInfo, ContextInterface $context, array|ContextInterface|RubyClassInterface ...$arguments): array
    {
        // @phpstan-ignore-next-line
        return self::applyAlignmentArgumentsByKeywords(
            $callInfo,
            $context,
            ...self::applySplatExpression(
                $context,

                // @phpstan-ignore-next-line
                ...$arguments
            ),
        );
    }

    /**
     * @param array<array<ContextInterface|RubyClassInterface>>|ContextInterface|RubyClassInterface ...$arguments
     *
     * @return (array<ContextInterface|RubyClassInterface>[]|ContextInterface|RubyClassInterface)[]
     */
    private static function applySplatExpression(ContextInterface $context, array|ContextInterface|RubyClassInterface ...$arguments): array
    {
        $newArguments = [];
        foreach ($arguments as $argument) {
            if (is_array($argument)) {
                $newArguments[] = Array_::createBy(
                    $argument,
                );

                continue;
            }

            $newArguments[] = $argument;
        }

        return $newArguments;
    }

    /**
     * @param array<array<ContextInterface|RubyClassInterface>>|ContextInterface|RubyClassInterface ...$arguments
     *
     * @return (array<ContextInterface|RubyClassInterface>[]|ContextInterface|RubyClassInterface)[]
     */
    private static function applyAlignmentArgumentsByKeywords(?CallInfoInterface $callInfo, ContextInterface $context, array|ContextInterface|RubyClassInterface ...$arguments): array
    {
        if (!$callInfo instanceof \RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface) {
            return $arguments;
        }

        $keywords = $callInfo->callData()->keywords();

        // A keyword is not available in callee arguments
        if ($keywords === null) {
            return $arguments;
        }

        $tempArguments = $newArguments = $arguments;
        $size = count($tempArguments);

        $localTable = $context->instructionSequence()
            ->body()
            ->info()
            ->variables();

        $size = count($tempArguments) - 1;
        foreach ($tempArguments as $i => $argument) {
            $keyword = array_pop($keywords)?->valueOf();
            if ($keyword === null) {
                $newArguments[$i] = $argument;

                continue;
            }

            $position = null;

            /**
             * lookup position in variables.
             *
             * @var Variable $variable
             */
            foreach ($localTable as $index => $variable) {
                if ($keyword === $variable->id->object->valueOf()) {
                    // Reverse position
                    $position = $size - $index;

                    break;
                }
            }

            $newArguments[$position] = $argument;
        }

        return $newArguments;
    }
}
