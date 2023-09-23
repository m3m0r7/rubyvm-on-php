<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\VM\Core\Runtime\RubyClass;

interface RubyClassImplementationInterface
{
    public function puts(RubyClass $object): RubyClass;

    public function exit(int $code = 0): void;
}
