<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Offset;

use RubyVM\VM\Exception\VerificationException;
use RubyVM\VM\Stream\Align;
use RubyVM\VM\Stream\SizeOf;

class Offset
{
    public function __construct(public readonly int $offset)
    {
        if ($this->offset < 0) {
            throw new VerificationException(sprintf('An offset cannot negative value (actual: %d)', $this->offset));
        }
    }

    public function increase(): Offset
    {
        return new Offset($this->offset + 1);
    }

    /**
     * NOTE: see also IBF_OBJBODY and IBF_ALIGNED_OFFSET(align, offset) implementation.
     */
    public function align(SizeOf|array|int $size): Offset
    {
        if (is_array($size)) {
            return $this->align(Align::alignOf($size));
        }

        if ($size instanceof SizeOf) {
            return new Offset(
                ((int) (($this->offset - 1) / $size->size()) + 1) * $size->size(),
            );
        }

        return new Offset(
            ((int) (($this->offset - 1) / $size) + 1) * $size,
        );
    }
}
