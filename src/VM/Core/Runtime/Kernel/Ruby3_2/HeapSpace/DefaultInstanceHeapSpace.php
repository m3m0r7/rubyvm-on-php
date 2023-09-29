<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\HeapSpace;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;

class DefaultInstanceHeapSpace extends UserlandHeapSpace
{
    /**
     * @var array<string>
     */
    protected static array $bindClassDirectories = [
        __DIR__ . '/../../../BasicObject',
        __DIR__ . '/../../../Entity',
    ];

    public function __construct()
    {
        parent::__construct();

        foreach (self::$bindClassDirectories as $directory) {
            $this->loadClasses($directory);
        }

        foreach (get_declared_classes() as $className) {
            $reflection = new \ReflectionClass($className);
            foreach ($reflection->getAttributes(BindAliasAs::class) as $attribute) {
                $this->userlandClasses
                    ->alias($attribute->getArguments()[0], $className);
            }
        }
    }

    protected function loadClasses(string $directory): void
    {
        foreach ((glob("{$directory}/*") ?: []) as $file) {
            if (is_dir($file)) {
                $this->loadClasses($file);

                continue;
            }

            require_once $file;
        }
    }
}
