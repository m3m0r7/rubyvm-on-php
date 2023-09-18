<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\InstructionSequence;

interface ObjectParameterInterface
{
    public function objectParamFlags(): ObjectParameterFlagsInterface;

    public function size(): int;

    public function leadNum(): int;

    public function optNum(): int;

    public function restStart(): int;

    public function postStart(): int;

    public function postNum(): int;

    public function blockStart(): int;

    public function optTable(): int;
}
