<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolize;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\RuntimeException;

#[BindAliasAs('Array')]
class Array_ extends Enumerable implements RubyClassInterface, Symbolize
{
    use Symbolizable;

    public function __construct(ArraySymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @param RubyClassInterface|SymbolInterface[] $values
     */
    public function new(RubyClassInterface|array $values = null): self
    {
        $this->symbol = new ArraySymbol(
            $values instanceof RubyClassInterface
                ? $values->valueOf()
                : ($values ?? []),
        );

        return $this;
    }

    public function each(CallInfoInterface $callInfo, ContextInterface $context): RubyClassInterface
    {
        $symbol = $this->symbol;

        assert($symbol instanceof ArraySymbol);

        for ($i = 0; $i < count($symbol); ++$i) {
            $executor = (new Executor(
                kernel: $context->kernel(),
                rubyClass: $context->self(),
                instructionSequence: $context->instructionSequence(),
                option: $context->option(),
                parentContext: $context,
            ));

            // Renew environment table
            $executor->context()
                ->renewEnvironmentTable();

            if (!$symbol[$i] instanceof RubyClassInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Out of index#%d in Array',
                        $i,
                    )
                );
            }

            $object = $symbol[$i]
                ->setRuntimeContext($executor->context())
                ->setUserlandHeapSpace($executor->context()->self()->userlandHeapSpace());

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
            if ($result->threw instanceof \Throwable) {
                throw $result->threw;
            }
        }

        return NilClass::createBy();
    }

    public function push(CallInfoInterface $callInfo, RubyClassInterface $object): self
    {
        // @phpstan-ignore-next-line
        $this->symbol[] = $object;

        return $this;
    }

    public static function createBy(mixed $value = []): self
    {
        return new self(new ArraySymbol($value));
    }

    public function offsetExists(mixed $offset): bool
    {
        assert($this->symbol instanceof ArraySymbol);

        return $this->symbol->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        assert($this->symbol instanceof ArraySymbol);

        return $this->symbol->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        assert($this->symbol instanceof ArraySymbol);
        $this->symbol->offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        assert($this->symbol instanceof ArraySymbol);
        $this->symbol->offsetUnset($offset);
    }

    public function getIterator(): \Traversable
    {
        assert($this->symbol instanceof ArraySymbol);

        return $this->symbol->getIterator();
    }

    public function count(): int
    {
        assert($this->symbol instanceof ArraySymbol);

        return $this->symbol->count();
    }

    #[BindAliasAs('+')]
    public function plus(CallInfoInterface $callInfo, RubyClassInterface $object): Array_
    {
        return Array_::createBy(
            [...$this->valueOf(), ...$object->valueOf()],
        );
    }
}
