<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\RubyClassExtendable;

class ArraySymbol implements SymbolInterface, \ArrayAccess, \Countable, \IteratorAggregate
{
    use RubyClassExtendable;

    public function __construct(
        public array $array,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            '[%d]',
            count($this->array)
        );
    }

    public function new(self|array $values = null): self
    {
        return new self(
            $values instanceof self
                ? $values->array
                : ($values ?? []),
        );
    }

    public function each(ContextInterface $context): void
    {
        for ($i = 0; $i < count($this->array); ++$i) {
            $executor = (new Executor(
                kernel: $context->kernel(),
                classImplementation: $context->self(),
                instructionSequence: $context->instructionSequence(),
                logger: $context->logger(),
                debugger: $context->debugger(),
                previousContext: $context,
            ));

            $executor->context()
                ->environmentTableEntries()
                ->get(Option::RSV_TABLE_INDEX_0)
                ->set(
                    Option::VM_ENV_DATA_SIZE,
                    (new NumberSymbol($this->array[$i]->number))
                        ->toObject()
                )
            ;

            $executor->context()
                ->appendTrace(ClassHelper::nameBy($this) . '#' . __FUNCTION__)
            ;

            $result = $executor->execute();

            // An occurred exception to be throwing
            if ($result->throwed) {
                throw $result->throwed;
            }
        }
    }

    public function push(SymbolInterface $symbol): SymbolInterface
    {
        $this->array[] = $symbol;

        return $this;
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::ARRAY,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: clone $this,
        );
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->array);
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

    public function count(): int
    {
        return count($this->array);
    }
}
