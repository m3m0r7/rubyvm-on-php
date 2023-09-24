<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

interface RubyClassifiable
{
    public function toBeRubyClass(): RubyClassInterface;
}
