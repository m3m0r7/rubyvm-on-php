<?php
declare(strict_types=1);
namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\FileStreamHandlerException;

class StringStreamHandler implements StreamHandlerInterface
{
    use StreamGeneric;

    public function __construct(public readonly string $string)
    {
        $this->handle = fopen('php://memory', 'rb');
        fwrite($this->handle, $string);
        rewind($this->handle);
    }

    public function size(): ?int
    {
        return strlen($this->string);
    }
}
