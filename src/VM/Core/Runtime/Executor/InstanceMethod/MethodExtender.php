<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\InstanceMethod;

use RubyVM\VM\Core\Helper\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Exception\ClassNotFoundException;

class MethodExtender
{
    private readonly ExtendedMethodEntries $extendedMethodEntries;

    public function __construct(private readonly ClassExtender $classExtender, private readonly string $className, private readonly string $bindClassName)
    {
        $this->extendedMethodEntries = new ExtendedMethodEntries();
    }

    public function classExtender(): ClassExtender
    {
        return $this->classExtender;
    }

    public function call(string $name, array $arguments): ExecutedResult
    {
        $extendedMethod = $this
            ->extendedMethodEntries
            ->get($name);

        if ($extendedMethod === null) {
            throw new ClassNotFoundException(
                sprintf(
                    'Not found extendable class (%s)',
                    $this->className,
                ),
            );
        }
        $context = $extendedMethod->context;

        $executor = (new Executor(
            kernel: $context->kernel(),
            main: $context->self(),
            instructionSequence: $context->instructionSequence(),
            logger: $context->logger(),
            debugger: $context->debugger(),
            previousContext: $context,
        ));

        $envIndex = LocalTableHelper::calculateFirstLocalTableIndex(
            $context,
            $arguments,
        );

        foreach ($arguments as $index => $argument) {
            $executor->context()
                ->environmentTableEntries()
                ->get(Option::RSV_TABLE_INDEX_0)
                ->set(
                    $envIndex + $index,
                    $argument->toObject(),
                );
        }

        $executor->context()
            ->appendTrace($this->className . '#' . $name)
        ;

        $result = $executor->execute();

        // An occurred exception to be throwing
        if ($result->throwed) {
            throw $result->throwed;
        }

        return $result;
    }

    public function extend(string $name, ContextInterface $context): self
    {
        $this->extendedMethodEntries->set($name, new ExtendedMethodEntry($context));
        return $this;
    }
}
