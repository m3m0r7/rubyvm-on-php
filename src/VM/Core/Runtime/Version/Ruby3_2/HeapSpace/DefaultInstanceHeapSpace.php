<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\HeapSpace;

use RubyVM\VM\Core\Runtime\UserlandHeapSpace;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\NumberSymbol;

class DefaultInstanceHeapSpace extends UserlandHeapSpace
{
    protected static array $bindClassNames = [
        'Array' => ArraySymbol::class,
    ];

    protected static array $bindAliasesMethods = [
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

        foreach (static::$bindClassNames as $originalClassName => $bindClassName) {
            $this->userlandClasses
                ->alias($originalClassName, ArraySymbol::class);
        }

        foreach (static::$bindAliasesMethods as [$classNames]) {
            foreach ($classNames as $className) {
                $this->userlandClasses
                    ->set($className, new UserlandHeapSpace());
            }
        }

        foreach (static::$bindAliasesMethods as [$classNames, $originalMethodName, $bindMethodName]) {
            foreach ($classNames as $className) {
                $this->userlandClasses
                    ->get($className)
                    ->userlandMethods()
                    ->set($originalMethodName, $bindMethodName);
            }
        }
    }
}
