<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\ID;

interface CallDataInterface
{
    public function flag(): int;

    public function mid(): ID;

    public function argumentsCount(): int;
}
