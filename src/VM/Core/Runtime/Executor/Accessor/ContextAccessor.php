<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Accessor;

use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Exception\RuntimeException;

readonly class ContextAccessor implements ContextAccessorInterface
{
    public function __construct(protected ExecutedResult $executedResult) {}

    /**
     * @param mixed[] $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        $self = $this->executedResult->executor->context()->self();

        /**
         * @var ExecutedResult $executedResult
         */
        $executedResult = $self->{$name}(
            ...array_map(
                static fn ($value) => Translator::PHPToRuby($value),
                $arguments,
            ),
        );
        if ($executedResult->threw instanceof \Throwable) {
            throw $executedResult->threw;
        }

        if ($executedResult->returnValue === null) {
            throw new RuntimeException('Unexpected return value from the executed result');
        }

        return Translator::RubyToPHP($executedResult->returnValue);
    }
}
