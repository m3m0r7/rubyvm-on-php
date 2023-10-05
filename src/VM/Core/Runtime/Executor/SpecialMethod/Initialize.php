<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\SpecialMethod;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

class Initialize implements SpecialMethodInterface
{
    public function process(RubyClassInterface $class, ContextInterface $context, CallInfoInterface $callInfo, ?ExecutorInterface $block, mixed ...$arguments): mixed
    {
        $result = $class;

        if ($class->hasMethod('initialize')) {
            // @phpstan-ignore-next-line
            $class->initialize(...$arguments);
        } elseif ($class->hasMethod('new')) {
            // @phpstan-ignore-next-line
            $result = $class->new(...$arguments);
        }

        $context->vmStack()->push(
            new Operand(
                $result,
            ),
        );

        return $result;
    }
}
