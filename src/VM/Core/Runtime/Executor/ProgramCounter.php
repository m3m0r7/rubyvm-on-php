<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

class ProgramCounter
{
    private int $counter = 0;
    private ?int $previousCounter = null;

    public function increase(int $add = 1): int
    {
        $this->previousCounter = $this->counter;
        $this->counter += $add;

        return $this->counter;
    }

    public function pos(): int
    {
        return $this->counter;
    }

    public function set(int $newPos): int
    {
        $this->previousCounter = $this->counter;
        $this->counter = $newPos;

        return $newPos;
    }

    public function decrease(): int
    {
        $this->previousCounter = $this->counter;

        return --$this->counter;
    }

    public function previousPos(): ?int
    {
        return $this->previousCounter;
    }
}
