<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationEntry;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\NilSymbol;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Internal\Arithmetic;
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
        if ($symbol instanceof ArraySymbol) {
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
         * @var ContextInterface|null $context
         */
        $context = $this->userLandMethods[$name] ?? null;

        if ($context === null) {
            throw new OperationProcessorException(
                sprintf(
                    'Method not found %s#%s',
                    ClassHelper::nameBy($this),
                    $name,
                ),
            );
        }

        $executor = (new Executor(
            kernel: $context->kernel(),
            main: $context->self(),
            operationProcessorEntries: $context->operationProcessorEntries(),
            instructionSequence: $context->instructionSequence(),
            logger: $context->logger(),
            debugger: $context->debugger(),
            previousContext: $context,
        ));

        // FIXME: Is the logic correctly? Here is temporarily implementation.
        $envIndex = $this->tryToGetFirstLocalTableIndex(
            $context,
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
                );
        }

        return $executor->execute();
    }

    private function tryToGetFirstLocalTableIndex(ContextInterface $context): int
    {
        $entries = $context->instructionSequence()->body()->operationEntries;
        /**
         * @var OperationEntry $operationEntry
         */
        for ($i = 0; $i < count($entries); $i++) {
            if ($entries[$i]->insn !== Insn::GETLOCAL_WC_0) {
                continue;
            }
            /**
             * @var OperandEntry $operand
             */
            $operand = $entries[$i + 1];

            /**
             * @var NumberSymbol $symbol
             */
            $symbol = $operand->operand->symbol;
            return $symbol->number;
        }

        return Option::VM_ENV_DATA_SIZE;
    }
}
