<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\InstanceMethod;

use RubyVM\VM\Core\Runtime\RubyClassExtendable;
use RubyVM\VM\Core\Runtime\RubyClassImplementationInterface;

class ExtendedClassEntry implements RubyClassImplementationInterface
{
    use RubyClassExtendable;

    public function __construct(protected ?array $extendableClasses = null)
    {
    }
}
