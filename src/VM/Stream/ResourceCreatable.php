<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\RubyVMBinaryStreamReaderException;

trait ResourceCreatable
{
    /**
     * @return resource
     */
    private function createResourceHandler(): mixed
    {
        return fopen('php://memory', 'r+b');
    }

    private function createResourceHandlerByStream(mixed $resource): mixed
    {
        ['seekable' => $seekable] = stream_get_meta_data($resource);

        if ($seekable === false) {
            throw new RubyVmBinaryStreamReaderException(
                'The resource is cannot duplication',
            );
        }

        $resource = $this->reader->streamHandler()->resource();
        $pipe = $this->createResourceHandler();

        rewind($resource);
        stream_copy_to_stream(
            $resource,
            $pipe,
            $this->reader->streamHandler()->size(),
            0,
        );
        rewind($pipe);

        return $pipe;
    }
}
