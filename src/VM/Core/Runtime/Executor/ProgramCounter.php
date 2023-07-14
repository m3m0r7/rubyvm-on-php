<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

class ProgramCounter
{
    private int $counter = 0;

    public function increase(): int
    {
        return ++$this->counter;
    }

    public function pos(): int
    {
        return $this->counter;
    }

    public function set(int $newPos): int
    {
        $this->counter = $newPos;
        return $newPos;
    }

    public function decrease(): int
    {
        return --$this->counter;
    }
}
