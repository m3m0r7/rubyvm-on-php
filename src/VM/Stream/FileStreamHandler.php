<?php
declare(strict_types=1);
namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\FileStreamHandlerException;

class FileStreamHandler implements StreamHandlerInterface
{
    use StreamGeneric;

    public function __construct(public readonly string $path)
    {
        $this->handle = fopen($path, 'rb');
    }


    public function size(): ?int
    {
        return filesize($this->path);
    }
}
