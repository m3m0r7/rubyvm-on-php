<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Entry\EntryType;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethod\Initialize;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethod\SpecialMethodInterface;

class SpecialMethodCallerEntries extends AbstractEntries
{
    public function __construct(public array $items = [])
    {
        parent::__construct($items);

        $this->set('new', new Initialize());
    }

    public function verify(mixed $value): bool
    {
        return $value instanceof SpecialMethodInterface;
    }

    protected function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
