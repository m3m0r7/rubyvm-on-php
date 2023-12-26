<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Debugger;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\FalseClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\TrueClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Undefined;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Void_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operation;

trait DebugFormat
{
    /**
     * @param array<Operand|Operation|RubyClassInterface> $entries
     */
    private static function getEntriesAsString(array $entries): string
    {
        $result = [];
        foreach ($entries as $index => $item) {
            $string = ClassHelper::nameBy($item);

            if ($item instanceof RubyClassInterface) {
                $string .= "({$item})";
            } elseif ($item instanceof Operation) {
                $string .= "({$item->insn->name()})";
            } elseif ($item instanceof Operand && $item->operand instanceof RubyClassInterface) {
                $string = ClassHelper::nameBy($item->operand);

                $string .= match ($item->operand::class) {
                    TrueClass::class,
                    FalseClass::class,
                    NilClass::class,
                    Void_::class,
                    Undefined::class => '',
                    default => "({$item->operand})",
                };
            }

            $result[] = $string . "#{$index}";
        }

        return rtrim(
            implode(', ', $result),
            ", \t\n\r\0\x0B",
        );
    }
}
