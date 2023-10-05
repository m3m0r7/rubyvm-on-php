<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\Attribute\WithContext;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;

#[BindAliasAs('Proc')]
class Proc extends Object_ implements RubyClassInterface
{
    public function __construct(private ?ContextInterface $procContext) {}

    #[WithContext]
    public function new(?ContextInterface $procContext): RubyClassInterface
    {
        $this->procContext = $procContext;

        return $this;
    }

    public function call(RubyClassInterface ...$arguments): RubyClassInterface|null
    {
        if (!$this->procContext instanceof \RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface) {
            return NilClass::createBy();
        }

        $executor = new Executor(
            kernel: $this->context()->kernel(),
            rubyClass: $this->context()->self(),
            instructionSequence: $this->procContext->instructionSequence(),
            option: $this->context()->option(),
            parentContext: $this->context(),
        );

        $executor->context()
            ->renewEnvironmentTable();

        $localTableSize = $this
            ->procContext
            ->instructionSequence()
            ->body()
            ->info()
            ->localTableSize();

        $argumentSize = count($arguments);
        $pos = $localTableSize - $argumentSize;
        for ($i = 0; $i < $argumentSize; ++$i) {
            $executor
                ->context()
                ->environmentTable()
                ->set(
                    Option::VM_ENV_DATA_SIZE + $pos + $i,
                    $arguments[$i],
                );
        }

        $result = $executor->execute();

        if ($result->threw instanceof \Throwable) {
            throw $result->threw;
        }

        return $result->returnValue;
    }

    public static function createBy(mixed $value = null): self
    {
        return new self($value);
    }
}
