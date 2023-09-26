<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Entity\Class_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Essential\MainInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceInterface;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class Lambda implements MainInterface
{
    use ShouldBeRubyClass;

    protected ?EntityInterface $entity = null;

    public function __construct(private readonly InstructionSequenceInterface $instructionSequence) {}

    public function entity(): EntityInterface
    {
        return $this->entity ??= Class_::createBy(new StringSymbol('lambda'));
    }

    public function __toString(): string
    {
        return 'lambda';
    }

    public function call(RubyClassInterface ...$arguments): RubyClassInterface|null
    {
        $executor = new Executor(
            kernel: $this->context()->kernel(),
            rubyClass: $this->context()->self(),
            instructionSequence: $this->instructionSequence,
            option: $this->context()->option(),
            debugger: $this->context()->debugger(),
            previousContext: $this->context(),
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
}
