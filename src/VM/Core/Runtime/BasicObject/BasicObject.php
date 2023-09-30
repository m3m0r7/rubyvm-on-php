<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\Entity\Class_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Entity\String_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

abstract class BasicObject implements RubyClassInterface
{
    use ShouldBeRubyClass;

    protected ?EntityInterface $entity = null;

    public function entity(): EntityInterface
    {
        return $this->entity ??= Class_::createBy(new StringSymbol($this->className()));
    }

    public function className(): string
    {
        $classNames = explode('\\', static::class);

        return array_pop($classNames);
    }

    #[BindAliasAs('to_s')]
    public function toString(): String_
    {
        return String_::createBy(
            (string) $this,
        );
    }

    public function __toString(): string
    {
        return $this->className();
    }
}
