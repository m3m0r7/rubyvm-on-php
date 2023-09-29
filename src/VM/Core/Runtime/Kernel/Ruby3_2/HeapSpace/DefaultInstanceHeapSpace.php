<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\HeapSpace;

use RubyVM\VM\Core\Runtime\Entity\Array_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
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
    protected static array $bindAliasesMethods = [];

    public function __construct()
    {
        parent::__construct();

        foreach (static::$bindClassNames as $originalClassName => $bindClassName) {
            $this->userlandClasses
                ->alias($originalClassName, $bindClassName);
        }
    }
}
