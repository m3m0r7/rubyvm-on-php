<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\Entity\Entityable;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ClassSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolSymbol;

#[BindAliasAs('Class')]
class Class_ extends Object_ implements RubyClassInterface
{
    use Entityable;

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

    public static function of(SymbolSymbol $symbol, ContextInterface $context): RubyClassInterface
    {
        return static::$classes[$context->modulePath((string) $symbol)] ??= (new self(new ClassSymbol($symbol)));
    }
}
