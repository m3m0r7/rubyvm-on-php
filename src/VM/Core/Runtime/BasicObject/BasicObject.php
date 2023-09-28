<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject;

use RubyVM\VM\Core\Runtime\Entity\Class_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

abstract class BasicObject implements RubyClassInterface
{
    use ShouldBeRubyClass;

    protected ?EntityInterface $entity = null;

    public function entity(): EntityInterface
    {
        return $this->entity ??= Class_::createBy(new StringSymbol((string) $this));
    }

    public function __toString(): string
    {
        $classNames = explode('\\', static::class);

        return array_pop($classNames);
    }
}
