<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence;

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
    ) {
    }
}
