<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

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
                OperandEntry::class => match (($item->operand)::class) {
                    Object_::class => (string) $item->operand->symbol,
                    SymbolInterface::class => (string) $item->operand,
                    default => 'none',
                },
                default => 'none',
            } . '>';
        }
        return implode(', ', $result);
    }
}
