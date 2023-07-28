<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;

interface RubyClassAggregationImplementationInterface
{
    public function toClassImplementation(): RubyClassImplementationInterface;
}
