<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

class ExtendedClassEntry implements RubyClassImplementationInterface
{
    use RubyClassExtendable;

    public function __construct(public string $className)
    {
    }

    public function isBound(string $boundClassName): bool
    {
        return $this->className === $boundClassName;
    }
}
