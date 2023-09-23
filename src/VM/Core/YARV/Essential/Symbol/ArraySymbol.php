<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Exception\OperationProcessorException;

class ArraySymbol implements SymbolInterface, \ArrayAccess, \Countable, \IteratorAggregate
{
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

    public function new(RubyClass|array $values = null): self
    {
        return new self(
            $values instanceof RubyClass
                ? $values->symbol->valueOf()
                : ($values ?? []),
        );
    }

    public function each(ContextInterface $context): void
    {
        for ($i = 0; $i < count($this->array); ++$i) {
            $executor = (new Executor(
                kernel: $context->kernel(),
                rubyClass: $context->self(),
                instructionSequence: $context->instructionSequence(),
                logger: $context->logger(),
                debugger: $context->debugger(),
                previousContext: $context,
            ));

            $object = (new NumberSymbol($this->array[$i]->valueOf()))
                ->toRubyClass()
                ->setRuntimeContext($context)
                ->setUserlandHeapSpace($context->self()->userlandHeapSpace());

            $executor->context()
                ->environmentTable()
                ->set(
                    Option::VM_ENV_DATA_SIZE,
                    $object,
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

    public function push(RubyClass $object): SymbolInterface
    {
        $this->array[] = $object->symbol;

        return $this;
    }

    public function toRubyClass(): RubyClass
    {
        return new RubyClass(
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

    public function isTestable(): bool
    {
        throw new OperationProcessorException(sprintf('The symbol type `%s` is not implemented `test` processing yet', ClassHelper::nameBy($this)));
    }
}
