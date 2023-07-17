<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Structure;

use RubyVM\VM\Stream\SizeOf;

interface StructureInterface
{
    /**
     * @return array<string, SizeOf|int>
     */
    public static function structure(): array;
    public function __get(string $name): int|string|float;
}
