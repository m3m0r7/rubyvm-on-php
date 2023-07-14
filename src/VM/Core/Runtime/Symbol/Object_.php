<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\Offset\Offset;

class Object_
{
    public readonly ID $id;

    public function __construct(
        public Offset $offset,
        public ObjectInfo $info,
        public SymbolInterface $symbol,
    ) {
        $this->id = new ID($this);
    }
}
