<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

interface RubyClassImplementationInterface
{
    public function puts(RubyClassInterface $object): RubyClassInterface;

    public function exit(int $code = 0): void;
}
