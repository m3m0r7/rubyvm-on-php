<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Symbol;

class ID
{
    protected readonly int $value;

    public function __construct(Object_ $object)
    {
        $this->value = spl_object_id($object);
    }
}
