<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;

class Array_ extends Entity implements EntityInterface
{
    public function __construct(ArraySymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function new(RubyClass|array $values = null): self
    {
        $this->symbol = new ArraySymbol(
            $values instanceof RubyClass
                ? $values->entity->symbol()->valueOf()
                : ($values ?? []),
        );

        return $this;
    }

    public function each(ContextInterface $context): void
    {
        for ($i = 0; $i < count($this->symbol); ++$i) {
            $executor = (new Executor(
                kernel: $context->kernel(),
                rubyClass: $context->self(),
                instructionSequence: $context->instructionSequence(),
                option: $context->option(),
                debugger: $context->debugger(),
                previousContext: $context,
            ));

            $object = Number::createBy($this->symbol[$i]->valueOf())
                ->toBeRubyClass()
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

    public function push(RubyClass $object): self
    {
        $this->symbol[] = $object->entity->symbol();

        return $this;
    }

    public static function createBy(mixed $value = []): self
    {
        return new self(new ArraySymbol($value));
    }
}
