<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\SpecialMethod;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\RubyClassInterface;

class Initialize implements SpecialMethodInterface
{
    public function process(RubyClassInterface $class, ContextInterface $context, mixed ...$arguments): mixed
    {
        $result = $class;
        if ($class->hasMethod('initialize')) {
            $class->initialize(...$arguments);
        } else {
            $result = $class->new(...$arguments);
        }

        $context->vmStack()->push(
            new OperandEntry(
                $result,
            ),
        );

        return $result;
    }
}
