<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential;

use RubyVM\VM\Core\Runtime\RubyClass;

interface RubyClassifiable
{
    public function toRubyClass(): RubyClass;
}
