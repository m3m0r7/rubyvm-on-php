<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Context;

use RubyVM\VM\Core\Runtime\Executor\Debugger\DebugFormat;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Exception\VMStackException;

class VMStack implements \Countable
{
    use DebugFormat;

    protected array $items = [];

    public function pos(): int
    {
        return count($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function pop(): Operand
    {
        $item = array_pop($this->items);
        if ($item === null) {
            throw new VMStackException('The VMStack is empty');
        }

        return $item;
    }

    public function shift(): Operand
    {
        return array_shift(
            $this->items,
        );
    }

    public function dup(): self
    {
        $object = $this->pop();
        $this->push($object, clone $object);

        return $this;
    }

    public function push(Operand $entry, Operand ...$otherEntries): self
    {
        array_push(
            $this->items,
            $entry,
            ...$otherEntries,
        );

        return $this;
    }
}
