<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\EntityInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\VoidSymbol;
use RubyVM\VM\Exception\OperationProcessorException;

class Void_ extends Entity implements EntityInterface
{
    public function __construct(VoidSymbol $symbol)
    {
        $this->symbol = $symbol;
    }
}
