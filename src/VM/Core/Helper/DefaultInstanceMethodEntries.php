<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Entry\EntryType;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethod\Power;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethod\RightShift;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethod\ToInt;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethod\Xor_;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethodInterface;

final class DefaultInstanceMethodEntries extends AbstractEntries
{
    public function __construct(public array $items = [])
    {
        parent::__construct($items);

        $this->set(Xor_::name(), new Xor_());
        $this->set(RightShift::name(), new RightShift());
        $this->set(ToInt::name(), new ToInt());
        $this->set(Power::name(), new Power());
    }

    public function verify(mixed $value): bool
    {
        return $value instanceof InstanceMethodInterface;
    }

    public function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
