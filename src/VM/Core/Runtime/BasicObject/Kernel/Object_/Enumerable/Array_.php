<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\Entity\Entityable;
use RubyVM\VM\Core\Runtime\Entity\EntityHelper;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\RuntimeException;

#[BindAliasAs('Array')]
class Array_ extends Enumerable implements RubyClassInterface
{
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
                ? $values->entity()->symbol()->valueOf()
                : ($values ?? []),
        );

        return $this;
    }

    public function each(CallInfoInterface $callInfo, ContextInterface $context): RubyClassInterface
    {
        /**
         * @var ArraySymbol $symbol
         */
        $symbol = $this->symbol;
        for ($i = 0; $i < count($symbol); ++$i) {
            $executor = (new Executor(
                kernel: $context->kernel(),
                rubyClass: $context->self(),
                instructionSequence: $context->instructionSequence(),
                option: $context->option(),
                debugger: $context->debugger(),
                parentContext: $context,
            ));

            // Renew environment table
            $executor->context()
                ->renewEnvironmentTable();

            if (!$symbol[$i] instanceof SymbolInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Out of index#%d in Array',
                        $i,
                    )
                );
            }

            $object = EntityHelper::createEntityBySymbol($symbol[$i])

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
        $this->symbol[] = $object->entity()->symbol();

        return $this;
    }

    public static function createBy(mixed $value = []): self
    {
        return new self(new ArraySymbol($value));
    }
}
