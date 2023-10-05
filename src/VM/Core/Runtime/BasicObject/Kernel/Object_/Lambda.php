<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;

class Lambda extends Object_ implements RubyClassInterface
{
    public function __construct(private readonly InstructionSequenceInterface $instructionSequence) {}

    public function call(RubyClassInterface ...$arguments): RubyClassInterface|null
    {
        $executor = new Executor(
            kernel: $this->context()->kernel(),
            rubyClass: $this->context()->self(),
            instructionSequence: $this->instructionSequence,
            option: $this->context()->option(),
            parentContext: $this->context(),
        );

        $executor->context()
            ->renewEnvironmentTable();

        $localTableSize = $this
            ->instructionSequence
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
