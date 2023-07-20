<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;

class ClassDefinition
{
    public function __construct(
        public readonly int $flags,
        public readonly ContextInterface $context,
    ) {

    }
}
