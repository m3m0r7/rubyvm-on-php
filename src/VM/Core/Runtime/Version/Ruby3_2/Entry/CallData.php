<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry;

use RubyVM\VM\Core\Runtime\Executor\CallDataInterface;

class CallData implements CallDataInterface
{
    public function __construct(
        public readonly mixed $mid,
        public readonly int $flag,
        public readonly int $argc,
        public readonly ?array $keywords,
    ) {
    }
}
