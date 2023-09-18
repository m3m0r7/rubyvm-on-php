<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\InstanceMethod;

use RubyVM\VM\Core\Runtime\Executor\InstanceMethodInterface;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Symbol\BooleanSymbol;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

class StrictEquals implements InstanceMethodInterface
{
    use Validatable;

    public static function name(): string
    {
        return '===';
    }

    public function process(SymbolInterface $symbol, ...$arguments): SymbolInterface
    {
        /**
         * @var NumberSymbol $numberSymbol
         */
        $numberSymbol = $symbol;

        $this->validateType(
            NumberSymbol::class,
            $numberSymbol,
        );

        if (!isset($arguments[0])) {
            throw new OperationProcessorException('An argument is enough');
        }

        /**
         * @var NumberSymbol $value
         */
        $value = $arguments[0];

        $this->validateType(
            NumberSymbol::class,
            $value,
        );

        if ($numberSymbol->valueOf() === $value->valueOf()) {
            return new BooleanSymbol(true);
        }

        return new BooleanSymbol(false);
    }
}
