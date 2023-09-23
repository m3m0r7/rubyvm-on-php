<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

class ID
{
    protected readonly int $value;

    public function __construct(public readonly RubyClass $object)
    {
        $this->value = spl_object_id($object);
    }

    public function id(): int
    {
        return $this->value;
    }
}
