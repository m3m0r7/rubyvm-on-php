<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

class CallInfoEntry implements CallInfoEntryInterface
{
    public function __construct(
        public readonly ?CallDataInterface $callData = null,
    ) {
    }

    public function callData(): ?CallDataInterface
    {
        return $this->callData;
    }
}
