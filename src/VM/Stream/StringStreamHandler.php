<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\StreamHandlerException;

class StringStreamHandler implements StreamHandlerInterface
{
    use StreamGeneric;

    protected readonly string $string;

    public function __construct(string $string)
    {
        // Add EOF byte
        $this->string = $string .= "\x03";
        $this->handle = fopen('php://memory', 'r+b');
        fwrite($this->handle, $this->string);
        rewind($this->handle);
    }

    public function write(string $string): void
    {
        throw new StreamHandlerException('The StringStreamHandler does not provide writer');
    }

    public function size(): ?int
    {
        // Add EOF byte
        return strlen($this->string);
    }
}
