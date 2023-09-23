<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Debugger;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

trait DebugFormat
{
    public function __toString(): string
    {
        $targetItems = $this->items ?? [];

        $result = [];
        foreach ($targetItems as $index => $item) {
            $result[] = match ($item::class) {
                SymbolInterface::class => (string) $item,
                Operand::class => (string) match (($item->operand)::class) {
                    RubyClass::class => ClassHelper::nameBy($item->operand->entity) . "({$item->operand->entity})",
                    default => ClassHelper::nameBy($item->operand),
                },
                RubyClass::class => ClassHelper::nameBy($item->entity) . "({$item->entity})",
                default => 'unknown',
            } . "#{$index}";
        }

        return rtrim(
            implode(', ', $result),
            ", \t\n\r\0\x0B",
        );
    }
}
