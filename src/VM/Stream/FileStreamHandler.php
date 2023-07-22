<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\StreamHandlerException;

class FileStreamHandler implements StreamHandlerInterface
{
    use StreamGeneric;

    public function __construct(public readonly string $path)
    {
        $this->handle = fopen($path, 'r+b');
    }

    public function write(string $string): void
    {
        throw new StreamHandlerException('The FileStreamHandler does not provide writer');
    }

    public function size(): ?int
    {
        return filesize($this->path);
    }
}
