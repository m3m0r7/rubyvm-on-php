<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Structure;

use RubyVM\Stream\SizeOf;

interface StructureInterface
{
    /**
     * @return array<string, int|SizeOf>
     */
    public static function structure(): array;

    public function __get(string $name): float|int|string;
}
