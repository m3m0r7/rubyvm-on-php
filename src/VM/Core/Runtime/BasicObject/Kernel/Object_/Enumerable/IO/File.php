<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\IO;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\SymbolizeInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\RuntimeException;

#[BindAliasAs('File')]
class File extends IO implements RubyClassInterface, SymbolizeInterface
{
    public function __construct(protected NilSymbol|StringSymbol $path, protected StringSymbol $mode, protected NumberSymbol $permission)
    {
        $this->path = $path;
        $this->mode = $mode;
        $this->permission = $permission;
    }

    public function getIterator(): \Traversable
    {
        throw new RuntimeException('File cannot iterate');
    }

    public function offsetExists(mixed $offset): bool
    {
        throw new RuntimeException('File cannot exists');
    }

    public function offsetGet(mixed $offset): mixed
    {
        throw new RuntimeException('File cannot get');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('File cannot set');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('File cannot unset');
    }

    public function count(): int
    {
        throw new RuntimeException('File cannot count');
    }

    public static function read(String_ $path): String_
    {
        return String_::createBy(
            file_get_contents(
                (string) $path,
            ),
        );
    }

    public static function createBy(?string $path = null, ?string $mode = null, ?int $permission = null): self
    {
        return new self(
            $path === null
                ? new NilSymbol()
                : new StringSymbol($path),
            new StringSymbol($mode ?? 'r'),
            new NumberSymbol($permission ?? 0666),
        );
    }

    public function symbol(): SymbolInterface
    {
        return $this->path;
    }
}
