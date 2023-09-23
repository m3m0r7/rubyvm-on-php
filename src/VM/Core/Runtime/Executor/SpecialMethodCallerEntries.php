<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\Criterion\Entry\EntryType;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethod\Initialize;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethod\SpecialMethodInterface;

class SpecialMethodCallerEntries extends AbstractEntries
{
    protected static array $bindCallers = [
        'new' => Initialize::class,
    ];

    public function __construct(public array $items = [])
    {
        parent::__construct($items);

        foreach (static::map() as $name => $class) {
            $this->set($name, new $class());
        }
    }

    public function verify(mixed $value): bool
    {
        return $value instanceof SpecialMethodInterface;
    }

    protected function entryType(): EntryType
    {
        return EntryType::HASH;
    }

    public static function map(): array
    {
        return static::$bindCallers;
    }
}
