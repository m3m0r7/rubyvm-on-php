<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\HeapSpace;

use RubyVM\VM\Core\Runtime\Entity\Array_;
use RubyVM\VM\Core\Runtime\Entity\Boolean_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Entity\Number;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;

class DefaultInstanceHeapSpace extends UserlandHeapSpace
{
    /**
     * @var array<string, class-string<EntityInterface>>
     */
    protected static array $bindClassNames = [
        'Array' => Array_::class,
    ];

    /**
     * @var array{class-string<EntityInterface>[], string, string}[]
     */
    protected static array $bindAliasesMethods = [
        [[Number::class, Boolean_::class], 'to_s', 'toString'],
        [[Number::class, Boolean_::class], '**', 'power'],
        [[Number::class, Boolean_::class], '>>', 'rightShift'],
        [[Number::class, Boolean_::class], '^', 'xor'],
        [[Number::class, Boolean_::class], 'to_i', 'toInt'],
        [[Number::class, Boolean_::class], '===', 'compareStrictEquals'],
    ];

    public function __construct()
    {
        parent::__construct();

        foreach (static::$bindClassNames as $originalClassName => $bindClassName) {
            $this->userlandClasses
                ->alias($originalClassName, $bindClassName);
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
