<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorContext;
use RubyVM\VM\Core\Runtime\Option;

class RangeSymbol implements SymbolInterface, \ArrayAccess
{
    private array $array;

    public function __construct(
        private readonly NumberSymbol $begin,
        private readonly NumberSymbol $end,
        private readonly bool $excludeEnd,
        private readonly int $steps = 1,
    ) {
        if ($this->begin->valueOf() > $this->end->valueOf()) {
            $this->array = [];

            return;
        }

        $array = [];
        foreach (range(
            $this->begin->valueOf(),
            $this->end->valueOf() - ($this->excludeEnd ? 1 : 0),
            $this->steps,
        ) as $i) {
            $array[] = new NumberSymbol($i);
        }

        $this->array = $array;
    }

    public function valueOf(): array
    {
        return $this->array;
    }

    public function __toString(): string
    {
        return "{$this->begin->valueOf()}" . ($this->excludeEnd ? '...' : '..') . "{$this->end->valueOf()}";
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
                ->appendTrace(ClassHelper::nameBy($this) . '#' . __FUNCTION__);

            $localTableSize = $executor->context()->instructionSequence()->body()->data->localTableSize();
            $object = $number->toObject()
                ->setRuntimeContext($context)
                ->setUserlandHeapSpace($context->self()->userlandHeapSpace());

            $executor->context()
                ->environmentTable()
                ->set(
                    LocalTableHelper::computeLocalTableIndex(
                        $localTableSize,
                        Option::VM_ENV_DATA_SIZE + $localTableSize - 1,
                    ),
                    $object,
                );

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
