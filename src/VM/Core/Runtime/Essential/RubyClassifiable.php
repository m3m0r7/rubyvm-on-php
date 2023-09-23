<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\VM\Core\Runtime\RubyClass;

interface RubyClassifiable
{
    public function toRubyClass(): RubyClass;
}
