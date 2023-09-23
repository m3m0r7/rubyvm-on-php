<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\EntityInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\OffsetSymbol;
use RubyVM\VM\Exception\OperationProcessorException;

class Offset extends Entity implements EntityInterface
{
    public function __construct(OffsetSymbol $symbol)
    {
        $this->symbol = $symbol;
    }
}
