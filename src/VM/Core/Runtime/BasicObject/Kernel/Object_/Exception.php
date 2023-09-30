<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;

abstract class Exception extends Object_ implements RubyClassInterface
{
    protected RubyClassInterface $message;

    public static function createBy(mixed $value = null): self
    {
        return new static();
    }

    public function setMessage(RubyClassInterface $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function toBeRubyClass(): RubyClassInterface
    {
        return $this;
    }

    public function userlandHeapSpace(): UserlandHeapSpaceInterface
    {
        return $this->userlandHeapSpace ??= new UserlandHeapSpace();
    }

    #[BindAliasAs('to_s')]
    public function __toString(): string
    {
        return (string) $this
            ->message

            ->symbol()
            ->valueOf();
    }

    public function message(): RubyClassInterface
    {
        return $this->message;
    }
}
