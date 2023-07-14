<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

interface StreamHandlerInterface
{
    public function read(int $bytes): string;
    public function write(string $string): void;
    public function size(): ?int;
}
