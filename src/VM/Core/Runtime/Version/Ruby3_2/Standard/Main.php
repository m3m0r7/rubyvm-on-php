<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\NilSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;
use RubyVM\VM\Stream\StreamHandlerInterface;

class Main implements MainInterface
{
    protected array $userLandMethods = [];

    public function __construct(
        private readonly StreamHandlerInterface $stdOut,
        private readonly StreamHandlerInterface $stdIn,
        private readonly StreamHandlerInterface $stdErr,
    ) {
    }

    public function puts(SymbolInterface $symbol): SymbolInterface
    {
        $this->stdOut->write((string) $symbol);

        // The puts returns (nil)
        return new NilSymbol();
    }

    public function phpinfo(): void
    {
        $this->stdOut->write('PHP Version: ' . PHP_VERSION . "\n");
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
        ))->enableBreakpoint($context->executor()->breakPoint());

        /**
         * @var SymbolInterface $argument
         */
        foreach ($arguments as $index => $argument) {
            $executor->context()->environmentTableEntries()
                ->get(Option::RSV_TABLE_INDEX_0)
                ->set(Option::VM_ENV_DATA_SIZE + $index, $argument->toObject());
        }

        return $executor->execute();
    }
}
