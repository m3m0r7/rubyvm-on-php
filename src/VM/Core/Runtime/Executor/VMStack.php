<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Executor\Debugger\DebugFormat;
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

    public function pop(): OperandEntry
    {
        $item = array_pop($this->items);
        if ($item === null) {
            throw new VMStackException('The VMStack is empty');
        }

        return $item;
    }

    public function shift(): OperandEntry
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

    public function push(OperandEntry $entry, OperandEntry ...$otherEntries): self
    {
        array_push(
            $this->items,
            $entry,
            ...$otherEntries,
        );

        return $this;
    }
}
