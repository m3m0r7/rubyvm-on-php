<?php


declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\InstanceMethod;


use RubyVM\VM\Core\Runtime\Executor\ContextInterface;

class ExtendedMethodEntry
{
    public function __construct(
        public readonly ContextInterface $context,
    ) {

    }
}
