<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion;

use RubyVM\VM\Core\Runtime\Object_;

interface RubyClassImplementationInterface
{
    public function puts(Object_ $object): Object_;

    public function exit(int $code = 0): void;
}
