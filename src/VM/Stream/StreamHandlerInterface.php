<?php
declare(strict_types=1);
namespace RubyVM\VM\Stream;

interface StreamHandlerInterface
{
    public function read(int $bytes): string;
    public function size(): ?int;
}
