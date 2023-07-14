<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

class VMStack
{
    protected array $stacks = [];

    public function pos(): int
    {
        return count($this->stacks);
    }

    public function pop(): OperandEntry
    {
        return array_pop($this->stacks);
    }

    public function shift(): OperandEntry
    {
        return array_shift(
            $this->stacks,
        );
    }

    public function dup(): void
    {
        $object = $this->pop();
        $this->push($object, $object);
    }

    public function push(OperandEntry $entry, OperandEntry ...$otherEntries): void
    {
        array_push(
            $this->stacks,
            $entry,
            ...$otherEntries,
        );
    }
}
