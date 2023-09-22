<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Accessor;

use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;

readonly class ContextAccessor implements ContextAccessorInterface
{
    public function __construct(protected ExecutedResult $executedResult) {}

    public function __call(string $name, array $arguments): mixed
    {
        $self = $this->executedResult->executor->context()->self();

        /**
         * @var ExecutedResult $executedResult
         */
        $executedResult = $self->{$name}(
            ...array_map(
                fn ($value) => Translator::PHPToRuby($value),
                $arguments,
            ),
        );
        if ($executedResult->threw) {
            throw $executedResult->threw;
        }

        return Translator::RubyToPHP($executedResult->returnValue);
    }
}
