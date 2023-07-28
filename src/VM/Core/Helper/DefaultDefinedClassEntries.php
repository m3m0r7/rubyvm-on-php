<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Executor\DefinedClassEntries;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethod\ExtendedClassEntry;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;

final class DefaultDefinedClassEntries extends DefinedClassEntries
{
    public function __construct(public array $items = [])
    {
        parent::__construct($items);

        $this->set('Array', new ExtendedClassEntry([ArraySymbol::class]));
    }

}
