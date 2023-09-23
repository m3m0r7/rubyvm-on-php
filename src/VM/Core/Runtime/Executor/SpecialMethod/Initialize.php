<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\SpecialMethod;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Object_;
use RubyVM\VM\Core\YARV\Essential\RubyClassInterface;
use RubyVM\VM\Exception\RubyVMException;

class Initialize implements SpecialMethodInterface
{
    public function process(RubyClassInterface $class, ContextInterface $context, mixed ...$arguments): mixed
    {
        $result = $class;

        if (!$class instanceof Object_) {
            throw new RubyVMException('The passed class is not implemented an Object class');
        }

        if ($class->hasMethod('initialize')) {
            $class->initialize(...$arguments);
        } elseif ($class->hasMethod('new')) {
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
