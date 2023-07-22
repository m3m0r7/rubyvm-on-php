<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

trait DebugFormat
{
    public function __toString(): string
    {
        $targetItems = $this->items ?? [];

        $result = [
            count($targetItems),
        ];
        foreach ($targetItems as $index => $item) {
            $result[] = ClassHelper::nameBy($item) . '<' . match ($item::class) {
                SymbolInterface::class => (string) $item,
                OperandEntry::class => (string) match (($item->operand)::class) {
                    Object_::class => ClassHelper::nameBy($item->operand->symbol) . "({$item->operand->symbol})",
                    default => ClassHelper::nameBy($item->operand),
                },
                Object_::class => ClassHelper::nameBy($item->symbol) . "({$item->symbol})",
                default => 'unknown',
            } . "#{$index}>";
        }

        return sprintf(
            '[total: %s]',
            rtrim(
                implode(', ', $result),
                ", \t\n\r\0\x0B",
            ),
        );
    }
}
