<?php

declare(strict_types=1);

namespace RubyVM\Stream;

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
        $size = filesize($this->path);
        if ($size === false) {
            return null;
        }

        return $size;
    }

    public function isTerminated(): bool
    {
        return feof($this->handle);
    }

    public function resource(): mixed
    {
        return $this->handle;
    }
}
