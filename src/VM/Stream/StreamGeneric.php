<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\FileStreamHandlerException;

trait StreamGeneric
{
    protected readonly mixed $handle;

    public function read(int $bytes): string
    {
        if (0 === $bytes) {
            return '';
        }

        $read = fread($this->handle, $bytes);
        if (strlen($read) !== $bytes) {
            throw new FileStreamHandlerException(sprintf('Unexpected read binary size (expected %d byte(s) but %d byte(s) insufficiency)', $bytes, $bytes - strlen($read)));
        }

        return $read;
    }

    public function readAll(): string
    {
        rewind($this->handle);

        return stream_get_contents($this->handle);
    }

    public function pos(int $newPos = null, int $whence = SEEK_SET): int
    {
        if ($newPos === null) {
            return ftell($this->handle);
        }

        $result = fseek($this->handle, $newPos, $whence);
        if (-1 === $result) {
            throw new FileStreamHandlerException(sprintf('Failed to set renewed position %d', $newPos));
        }

        if ($result < $newPos && feof($this->handle)) {
            throw new FileStreamHandlerException(sprintf('The cursor has been moved end of stream (overflowed %d byte(s))', $newPos));
        }

        return $result;
    }
}
