<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Helper\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorContext;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\ShouldBeRubyClass;

class RangeSymbol implements SymbolInterface, \ArrayAccess, RubyClassInterface
{
    use ShouldBeRubyClass;
    private array $array;

    public function __construct(
        public readonly NumberSymbol $begin,
        public readonly NumberSymbol $end,
        public readonly bool $excludeEnd,
        public readonly int $steps = 1,
    ) {
        if ($this->begin->number > $this->end->number) {
            $this->array = [];

            return;
        }

        $array = [];
        foreach (range(
            $this->begin->number,
            $this->end->number - ($this->excludeEnd ? 1 : 0),
            $this->steps,
        ) as $i) {
            $array[] = new NumberSymbol($i);
        }

        $this->array = $array;
    }

    public function __toString(): string
    {
        return "{$this->begin->number}" . ($this->excludeEnd ? '...' : '..') . "{$this->end->number}";
    }

    public function each(OperationProcessorContext $context): SymbolInterface
    {
        /**
         * @var NumberSymbol $number
         */
        foreach ($this->array as $index => $number) {
            $executor = (new Executor(
                kernel: $context->kernel(),
                rubyClass: $context->self(),
                instructionSequence: $context->instructionSequence(),
                logger: $context->logger(),
                debugger: $context->debugger(),
                previousContext: $context,
            ));

            $executor->context()
                ->appendTrace(ClassHelper::nameBy($this) . '#' . __FUNCTION__)
            ;

            $localTableSize = $executor->context()->instructionSequence()->body()->data->localTableSize();

            $executor->context()
                ->environmentTable()
                ->set(
                    LocalTableHelper::computeLocalTableIndex(
                        $localTableSize,
                        Option::VM_ENV_DATA_SIZE + $localTableSize - 1,
                    ),
                    $number->toObject()
                )
            ;

            $result = $executor->execute();

            // An occurred exception to be throwing
            if ($result->threw) {
                throw $result->threw;
            }
        }

        return new NilSymbol();
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::STRUCT,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->array[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->array[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->array[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->array[$offset]);
    }
}
