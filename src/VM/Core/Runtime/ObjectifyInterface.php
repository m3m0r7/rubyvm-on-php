<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Symbol\Object_;

interface ObjectifyInterface
{
    public function toObject(): Object_;
}
