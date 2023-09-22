<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\HeapSpace;

use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\BooleanSymbol;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;

class DefaultInstanceHeapSpace extends UserlandHeapSpace
{
    public function __construct()
    {
        parent::__construct();

        // TODO: Refactor here

        $heapspace = new UserlandHeapSpace();
        $heapspace->userlandClasses->alias('Array', ArraySymbol::class);

        $heapspace->userlandMethods->set(
            'to_s',
            'toString',
        );
        $heapspace->userlandMethods->set(
            '**',
            'power',
        );
        $heapspace->userlandMethods->set(
            '>>',
            'rightShift',
        );
        $heapspace->userlandMethods->set(
            '^',
            'xor',
        );
        $heapspace->userlandMethods->set(
            'to_i',
            'toInt',
        );
        $heapspace->userlandMethods->set(
            'to_i',
            'toInt',
        );
        $heapspace->userlandMethods->set(
            '===',
            'compareStrictEquals',
        );

        $this->userlandClasses
            ->set(NumberSymbol::class, clone $heapspace);
        $this->userlandClasses
            ->set(BooleanSymbol::class, clone $heapspace);
    }
}
