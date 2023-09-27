<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

interface RubyClassImplementationInterface
{
    public function puts(CallInfoInterface $callInfo, RubyClassInterface $object): RubyClassInterface;

    public function exit(CallInfoInterface $callInfo, int $code = 0): never;
}
