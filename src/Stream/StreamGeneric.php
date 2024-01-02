<?php

declare(strict_types=1);

namespace RubyVM\Stream;

use RubyVM\VM\Exception\FileStreamHandlerException;

trait StreamGeneric
{
    protected readonly mixed $handle;

    public function read(int $bytes): string
    {
        if ($bytes === 0) {
            return '';
        }

        if ($bytes < 0) {
            throw new FileStreamHandlerException(sprintf('Unexpected byte size (the value is negative: %d, but expecting positive)', $bytes));
        }

        $read = fread($this->handle, $bytes);

        if ($read === false) {
            throw new FileStreamHandlerException('Unexpected byte size (stream cannot read)');
        }

        if (strlen($read) !== $bytes) {
            throw new FileStreamHandlerException(sprintf('Unexpected read binary size (expected %d byte(s) but %d byte(s) insufficiency)', $bytes, $bytes - strlen($read)));
        }

        return $read;
    }

    public function readAll(): string
    {
        rewind($this->handle);

        $read = stream_get_contents($this->handle);

        if ($read === false) {
            throw new FileStreamHandlerException('Unexpected byte size (stream cannot read)');
        }

        return $read;
    }

    public function pos(int $newPos = null, int $whence = SEEK_SET): int
    {
        if ($newPos === null) {
            $pos = ftell($this->handle);

            if ($pos === false) {
                throw new FileStreamHandlerException('Unexpected byte size (stream cannot read)');
            }

            return $pos;
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
