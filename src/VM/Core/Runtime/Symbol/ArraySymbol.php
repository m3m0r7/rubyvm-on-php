<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\ShouldBeRubyClass;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;

class ArraySymbol implements SymbolInterface, \ArrayAccess, \Countable, \IteratorAggregate, RubyClassInterface
{
    use ShouldBeRubyClass;

    public function __construct(
        private array $array,
    ) {}

    public function valueOf(): array
    {
        return $this->array;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s]',
            implode(', ', array_map(
                fn ($value) => (string) $value,
                $this->array,
            ))
        );
    }

    public function new(self|array $values = null): self
    {
        return (new self(
            $values instanceof self
                ? $values->array
                : ($values ?? []),
        ))->tryToSetUserlandHeapSpace($this->userlandHeapSpace);
    }

    public function each(ContextInterface $context): void
    {
        for ($i = 0; $i < count($this->array); ++$i) {
            $executor = (new Executor(
                kernel: $context->kernel(),
                rubyClass: $context->self(),
                instructionSequence: $context->instructionSequence(),
                logger: $context->logger(),
                userlandHeapSpace: $context->userlandHeapSpace(),
                debugger: $context->debugger(),
                previousContext: $context,
            ));

            $executor->context()
                ->environmentTable()
                ->set(
                    Option::VM_ENV_DATA_SIZE,
                    (new NumberSymbol($this->array[$i]->valueOf()))
                        ->setRuntimeContext($context)
                        ->tryToSetUserlandHeapSpace(new UserlandHeapSpace())
                        ->toObject()
                );

            $executor->context()
                ->appendTrace(ClassHelper::nameBy($this) . '#' . __FUNCTION__);

            $result = $executor->execute();

            // An occurred exception to be throwing
            if ($result->threw) {
                throw $result->threw;
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
