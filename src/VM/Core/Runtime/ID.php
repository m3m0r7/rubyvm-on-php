<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

class ID
{
    protected readonly int $value;

    public function __construct(public readonly SymbolInterface $object)
    {
        $this->value = spl_object_id($object);
    }

    public function id(): int
    {
        return $this->value;
    }
}
