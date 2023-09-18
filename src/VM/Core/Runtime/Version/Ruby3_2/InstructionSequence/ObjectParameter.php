<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\Runtime\InstructionSequence\ObjectParameterFlagsInterface;
use RubyVM\VM\Core\Runtime\InstructionSequence\ObjectParameterInterface;

class ObjectParameter implements ObjectParameterInterface
{
    public function __construct(
        public readonly ObjectParameterFlags $objectParamFlags,
        public readonly int $size,
        public readonly int $leadNum,
        public readonly int $optNum,
        public readonly int $restStart,
        public readonly int $postStart,
        public readonly int $postNum,
        public readonly int $blockStart,
        public readonly mixed $optTable,
    ) {}

    public function objectParamFlags(): ObjectParameterFlagsInterface
    {
        return $this->objectParamFlags;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function leadNum(): int
    {
        return $this->leadNum;
    }

    public function optNum(): int
    {
        return $this->optNum;
    }

    public function restStart(): int
    {
        return $this->restStart;
    }

    public function postStart(): int
    {
        return $this->postStart;
    }

    public function postNum(): int
    {
        return $this->postNum;
    }

    public function blockStart(): int
    {
        return $this->blockStart;
    }

    public function optTable(): int
    {
        return $this->optTable;
    }
}
