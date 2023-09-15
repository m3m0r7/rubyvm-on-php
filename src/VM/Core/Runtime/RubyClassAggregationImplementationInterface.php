<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

interface RubyClassAggregationImplementationInterface
{
    public function toClassImplementation(): RubyClassImplementationInterface;
}
