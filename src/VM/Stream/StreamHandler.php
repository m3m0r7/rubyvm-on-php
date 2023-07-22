<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\StreamHandlerException;

class StreamHandler implements StreamHandlerInterface
{
    use StreamGeneric;

    public function __construct(public readonly mixed $pipe)
    {
        if (!is_resource($pipe)) {
            throw new StreamHandlerException('The specified parameter is not a stream');
        }
        $this->handle = $pipe;
    }

    public function write(string $string): void
    {
        fwrite($this->handle, $string);
    }

    public function size(): ?int
    {
        return null;
    }
}
