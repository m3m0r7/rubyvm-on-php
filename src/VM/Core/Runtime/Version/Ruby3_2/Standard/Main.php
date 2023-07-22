<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperationEntry;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\NilSymbol;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\RangeSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;
use RubyVM\VM\Stream\StreamHandlerInterface;

class Main implements MainInterface
{
    protected array $userLandMethods = [];
    protected array $userLandClasses = [];

    public function __construct(
        private readonly StreamHandlerInterface $stdOut,
        private readonly StreamHandlerInterface $stdIn,
        private readonly StreamHandlerInterface $stdErr,
    ) {
    }

    public function puts(SymbolInterface $symbol): SymbolInterface
    {
        $string = '';
        if ($symbol instanceof ArraySymbol || $symbol instanceof RangeSymbol) {
            foreach ($symbol as $number) {
                $string .= "{$number}\n";
            }
        } elseif ($symbol instanceof NilSymbol) {
            // When an argument is a nil symbol, then displays a break only
            $string = "\n";
        } else {
            $string = (string) $symbol;
        }
        if (!str_ends_with($string, "\n")) {
            $string .= "\n";
        }

        $this->stdOut->write($string);

        // The puts returns (nil)
        return new NilSymbol();
    }

    public function phpinfo(): void
    {
        $this->stdOut->write('PHP Version: ' . PHP_VERSION . "\n");
    }

    public function exit(): void
    {
        exit;
    }

    public function class(NumberSymbol $flags, StringSymbol $className, ContextInterface $context): void
    {
        $this->userLandClasses[(string) $className] = new ClassDefinition(
            $flags->number,
            $context,
        );
    }

    public function def(StringSymbol $methodName, ContextInterface $context): void
    {
        $this->userLandMethods[(string) $methodName] = $context;
    }

    public function __call(string $name, array $arguments): ExecutedResult
    {
        /**
         * @var null|ContextInterface $context
         */
        $context = $this->userLandMethods[$name] ?? null;

        if (null === $context) {
            throw new OperationProcessorException(sprintf('Method not found %s#%s', ClassHelper::nameBy($this), $name));
        }

        $executor = (new Executor(
            kernel: $context->kernel(),
            main: $context->self(),
            operationProcessorEntries: $context->operationProcessorEntries(),
            instructionSequence: $context->instructionSequence(),
            logger: $context->logger(),
            debugger: $context->debugger(),
            previousContext: $context
                ->renewEnvironmentTableEntries(),
        ));

        $envIndex = $this->calculateFirstLocalTableIndex(
            $context,
            $arguments,
        );

        /**
         * @var SymbolInterface $argument
         */
        foreach ($arguments as $index => $argument) {
            $executor->context()->environmentTableEntries()
                ->get(Option::RSV_TABLE_INDEX_0)
                ->set(
                    $envIndex + $index,
                    $argument->toObject(),
                )
            ;
        }

        return $executor->execute();
    }

    private function calculateFirstLocalTableIndex(ContextInterface $context, array $arguments = []): int
    {
        // FIXME: Is the logic correctly? Here is temporarily implementation.

        $size = 0;
        $ignoredIndexes = [];

        $entries = $context->instructionSequence()->body()->operationEntries;

        $min = null;
        for ($i = 0; $i < count($entries); ++$i) {
            /**
             * @var OperationEntry $operationEntry
             */
            $operationEntry = $entries[$i];
            if (!$entries[$i] instanceof OperationEntry) {
                continue;
            }
            if ($operationEntry->insn === Insn::SETLOCAL_WC_0 || $operationEntry->insn === Insn::SETLOCAL_WC_1) {
                ++$i;
                $number = $entries[$i]->operand->symbol->number;
                $ignoredIndexes[] = $number;
            } elseif ($operationEntry->insn === Insn::GETLOCAL_WC_0 || $operationEntry->insn === Insn::GETLOCAL_WC_1) {
                ++$i;
                $number = $entries[$i]->operand->symbol->number;
                if (in_array($number, $ignoredIndexes, true)) {
                    continue;
                }
                if ($min === null || $min > $number) {
                    $min = $number;
                }
            }
        }

        if ($min !== null) {
            return $min;
        }

        return Option::VM_ENV_DATA_SIZE + $size;
    }
}
