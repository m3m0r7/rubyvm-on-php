<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\HeapSpace;

use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\BooleanSymbol;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;

class DefaultInstanceHeapSpace extends UserlandHeapSpace
{
    protected array $bindClassNames = [
        'Array' => ArraySymbol::class,
    ];

    protected array $bindAliasesMethods = [
        [[NumberSymbol::class, BooleanSymbol::class], 'to_s', 'toString'],
        [[NumberSymbol::class, BooleanSymbol::class], '**', 'power'],
        [[NumberSymbol::class, BooleanSymbol::class], '>>', 'rightShift'],
        [[NumberSymbol::class, BooleanSymbol::class], '^', 'xor'],
        [[NumberSymbol::class, BooleanSymbol::class], 'to_i', 'toInt'],
        [[NumberSymbol::class, BooleanSymbol::class], '===', 'compareStrictEquals'],
    ];

    public function __construct()
    {
        parent::__construct();

        foreach ($this->bindClassNames as $originalClassName => $bindClassName) {
            $this->userlandClasses
                ->alias($originalClassName, ArraySymbol::class);
        }

        foreach ($this->bindAliasesMethods as [$classNames]) {
            foreach ($classNames as $className) {
                $this->userlandClasses
                    ->set($className, new UserlandHeapSpace());
            }
        }

        foreach ($this->bindAliasesMethods as [$classNames, $originalMethodName, $bindMethodName]) {
            foreach ($classNames as $className) {
                $this->userlandClasses
                    ->get($className)
                    ->userlandMethods()
                    ->set($originalMethodName, $bindMethodName);
            }
        }
    }
}
