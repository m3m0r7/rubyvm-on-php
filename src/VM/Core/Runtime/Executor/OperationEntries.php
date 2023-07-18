<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;

class OperationEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof OperationEntry ||
            $value instanceof OperandEntry ||
            $value instanceof UnknownEntry;
    }

    public function __toString(): string
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = ClassHelper::nameBy($item) . '<' . match ($item::class) {
                OperationEntry::class => sprintf(
                    '%s<0x%02x>',
                    strtolower($item->insn->name),
                    $item->insn->value
                ),
                default => 'none',
            } . '>';
        }
        return implode(', ', $result);
    }
}
