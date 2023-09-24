<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\Criterion\Entry\EntryType;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethod\Initialize;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethod\SpecialMethodInterface;

class SpecialMethodCallerEntries extends AbstractEntries
{

    /**
     * @var array<string, class-string<SpecialMethodInterface>>
     */
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

    /**
     * @return array<string, class-string<SpecialMethodInterface>>
     */
    public static function map(): array
    {
        return static::$bindCallers;
    }
}
