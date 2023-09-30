<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;

class Lambda extends Object_ implements RubyClassInterface
{
    public function __construct(private readonly InstructionSequenceInterface $instructionSequence) {}

    public function call(CallInfoInterface $callInfo, RubyClassInterface ...$arguments): RubyClassInterface|null
    {
        $executor = new Executor(
            kernel: $this->context()->kernel(),
            rubyClass: $this->context()->self(),
            instructionSequence: $this->instructionSequence,
            option: $this->context()->option(),
            debugger: $this->context()->debugger(),
            parentContext: $this->context(),
        );

        $localTableSize = $this
            ->instructionSequence
            ->body()
            ->info()
            ->localTableSize();

        $argumentSize = count($arguments);
        for ($i = 0; $i < $argumentSize; ++$i) {
            $executor
                ->context()
                ->environmentTable()
                ->set(
                    Option::VM_ENV_DATA_SIZE + $i,
                    $arguments[$i],
                );
        }

        $result = $executor->execute();

        if ($result->threw instanceof \Throwable) {
            throw $result->threw;
        }

        return $result->returnValue;
    }

    public static function createBy(mixed $value = null): EntityInterface
    {
        return new self($value);
    }
}
