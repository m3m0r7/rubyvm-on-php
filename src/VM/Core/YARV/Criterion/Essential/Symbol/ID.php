<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Essential\Symbol;

use RubyVM\VM\Core\Runtime\Object_;

class ID
{
    protected readonly int $value;

    public function __construct(public readonly Object_ $object)
    {
        $this->value = spl_object_id($object);
    }

    public function id(): int
    {
        return $this->value;
    }
}
