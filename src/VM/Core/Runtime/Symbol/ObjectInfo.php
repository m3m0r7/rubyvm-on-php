<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class ObjectInfo
{
    public function __construct(
        public readonly SymbolType $type,
        public readonly int $specialConst,
        public readonly int $frozen,
        public readonly int $internal,
    ) {
    }

    public static function none(): self
    {
        return new ObjectInfo(
            type: SymbolType::NONE,
            specialConst: 0,
            frozen: 0,
            internal: 0,
        );
    }
}
