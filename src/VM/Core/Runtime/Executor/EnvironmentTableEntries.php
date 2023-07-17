<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Exception\OperationProcessorException;

class EnvironmentTableEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof EnvironmentTable;
    }

    public function get(mixed $index): EnvironmentTable
    {
        if (!isset($this->items[$index])) {
            $this->items[$index] = new EnvironmentTable();
        }
        return parent::get($index);
    }

    public function create(mixed $index): void
    {
        $this->set($index, null);
    }

    public function set(mixed $index, mixed $value): void
    {
        parent::set(
            $index,
            $value ?? new EnvironmentTable(),
        );
    }
}
