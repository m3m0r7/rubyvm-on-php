<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\SymbolizeInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\RuntimeException;

#[BindAliasAs('Dir')]
class Dir extends Enumerable implements RubyClassInterface, SymbolizeInterface
{
    use Symbolizable;
    protected ?\ArrayIterator $iterator = null;

    /**
     * @var \DirectoryIterator[]
     */
    protected array $files = [];

    protected string $directory;

    public function __construct(protected SymbolInterface $symbol)
    {
        $this->directory = (string) $this->symbol;
        $this->files = array_values(
            iterator_to_array(
                new \DirectoryIterator($this->symbol->valueOf()),
            )
        );
    }

    public static function pwd(): RubyClassInterface
    {
        return String_::createBy(getcwd());
    }

    public function getIterator(): \ArrayIterator
    {
        return $this->iterator ??= new \ArrayIterator($this->files);
    }

    public function offsetExists(mixed $offset): bool
    {
        return (bool) $this->offsetGet($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->files[$offset] ?? throw new RuntimeException(
            sprintf('File not found #%s', $offset),
        );
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
        return count($this->files);
    }

    public static function createBy(?string $directory = null): self
    {
        return new self(new StringSymbol((string) ($directory ?? getcwd())));
    }
}
