<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolize;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;

class Range extends Enumerable implements RubyClassInterface, Symbolize
{
    use Symbolizable;

    public function __construct(RangeSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function each(CallInfoInterface $callInfo, ContextInterface $context): RubyClassInterface
    {
        foreach ($this->symbol->valueOf() as $index => $number) {
            $executor = (new Executor(
                kernel: $context->kernel(),
                rubyClass: $context->self(),
                instructionSequence: $context->instructionSequence(),
                option: $context->option(),
                parentContext: $context,
            ));

            $executor->context()
                ->appendTrace(ClassHelper::nameBy($this) . '#' . __FUNCTION__);

            $localTableSize = $executor->context()->instructionSequence()->body()->info()->localTableSize();
            $object = (new Integer_($number))

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
            if ($result->threw instanceof \Throwable) {
                throw $result->threw;
            }
        }

        return NilClass::createBy();
    }

    public static function createBy(mixed ...$value): self
    {
        return new self(new RangeSymbol(...$value));
    }

    public function offsetExists(mixed $offset): bool
    {
        assert($this->symbol instanceof RangeSymbol);

        return $this->symbol->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        assert($this->symbol instanceof RangeSymbol);

        return $this->symbol->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        assert($this->symbol instanceof RangeSymbol);
        $this->symbol->offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        assert($this->symbol instanceof RangeSymbol);
        $this->symbol->offsetUnset($offset);
    }

    public function getIterator(): \Traversable
    {
        assert($this->symbol instanceof RangeSymbol);

        return $this->symbol->getIterator();
    }

    public function count(): int
    {
        assert($this->symbol instanceof RangeSymbol);

        return $this->symbol->count();
    }
}
