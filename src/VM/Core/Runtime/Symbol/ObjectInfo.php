<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;

class ObjectInfo
{
    public function __construct(
        public readonly SymbolType $type,
        public readonly int $specialCount,
        public readonly int $frozen,
        public readonly int $internal,
    ) {}
}
