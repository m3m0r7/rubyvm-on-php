<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Essential\EntityInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\VoidSymbol;

class Void_ extends Entity implements EntityInterface
{
    public function __construct(VoidSymbol $symbol)
    {
        $this->symbol = $symbol;
    }
}
