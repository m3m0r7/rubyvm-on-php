<?php

declare(strict_types=1);

namespace RubyVM\VM\Exception;

class ExitException extends RubyVMException
{
    public function __construct(int $code = 0)
    {
        parent::__construct(
            'exit',
            $code,
        );
    }
}
