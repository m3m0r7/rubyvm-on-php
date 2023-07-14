<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\Runtime\Executor\Keyword;
use RubyVM\VM\Core\Runtime\InstructionSequence\ObjectParameterFlagsInterface;

class ObjectParameterFlags implements ObjectParameterFlagsInterface
{
    public function __construct(
        public readonly bool $hasLead,
        public readonly bool $hasOpt,
        public readonly bool $hasRest,
        public readonly bool $hasPost,
        public readonly Keyword $keyword,
        public readonly bool $hasKeyword,
        public readonly bool $hasKeywordRest,
        public readonly bool $hasBlock,
        public readonly bool $ambiguousParam,
        public readonly bool $acceptsNoKeywordArg,
        public readonly bool $ruby2Keywords,
    ) {
    }
}
