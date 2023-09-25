<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;
use RubyVM\VM\Core\YARV\Essential\Symbol\ClassSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class Class_ extends Entity implements EntityInterface
{
    /**
     * @var RubyClassInterface[]
     */
    public static array $classes = [];

    public function __construct(ClassSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = null): self
    {
        return new self(new ClassSymbol($value));
    }

    public static function of(StringSymbol $symbol): RubyClassInterface
    {
        return static::$classes[(string) $symbol] ??= (new self(new ClassSymbol($symbol)))
            ->toBeRubyClass();
    }
}
