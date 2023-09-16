<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Accessor;

use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;

class Method implements AccessorInterface
{
    public function __construct(protected readonly ExecutedResult $executedResult)
    {
    }

    public function __call(string $name, array $arguments): mixed
    {
        $self = $this->executedResult->executor->context()->self();

        /**
         * @var ExecutedResult $executedResult
         */
        $executedResult = $self->{$name}(
            ...array_map(
                fn ($value) => Translator::PHPToRuby($value)
                    ->symbol,
                $arguments,
            ),
        );
        if ($executedResult->threw) {
            throw $executedResult->threw;
        }

        return Translator::RubyToPHP($executedResult->returnValue);
    }
}
