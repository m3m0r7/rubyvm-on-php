<?php

declare(strict_types=1);

namespace RubyVM\Stream;

use RubyVM\VM\Exception\FileStreamHandlerException;
use RubyVM\VM\Exception\RubyVMBinaryStreamReaderException;

trait ResourceCreatable
{
    /**
     * @return resource
     */
    private function createResourceHandler()
    {
        $resource = fopen('php://memory', 'r+b');

        if ($resource === false) {
            throw new FileStreamHandlerException('The resource cannot create');
        }

        return $resource;
    }

    /**
     * @param resource $resource
     *
     * @return resource
     */
    private function createResourceHandlerByStream($resource, ?int $size = null)
    {
        ['seekable' => $seekable] = stream_get_meta_data($resource);

        if ($seekable === false) {
            throw new RubyVmBinaryStreamReaderException(
                'The resource is cannot duplication',
            );
        }

        $pipe = $this->createResourceHandler();

        rewind($resource);
        stream_copy_to_stream(
            $resource,
            $pipe,
            $size,
            0,
        );
        rewind($pipe);

        return $pipe;
    }
}
