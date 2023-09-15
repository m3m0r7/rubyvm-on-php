<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperationEntry;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Option;

final class LocalTableHelper
{

    /**
     * @see https://github.com/ruby/ruby/blob/ruby_3_2/yjit/src/codegen.rs#L1482
     * @see https://github.com/ruby/ruby/blob/ruby_3_2/yjit/src/codegen.rs#L1584
     */
    public static function calculateFirstLocalTableIndex(ContextInterface $context, array $arguments = []): int
    {
        // TODO: I will rewrite here
        // FIXME: Is the logic correctly? Here is temporarily implementation.

        $size = 0;
        $ignoredIndexes = [];

        $entries = $context->instructionSequence()->body()->operationEntries;

        $min = null;
        for ($i = 0; $i < count($entries); ++$i) {
            /**
             * @var OperationEntry $operationEntry
             */
            $operationEntry = $entries[$i];
            if (!$entries[$i] instanceof OperationEntry) {
                continue;
            }
            if ($operationEntry->insn === Insn::SETLOCAL_WC_0 || $operationEntry->insn === Insn::SETLOCAL_WC_1) {
                ++$i;
                $number = $entries[$i]->operand->symbol->number;
                $ignoredIndexes[] = $number;
            } elseif ($operationEntry->insn === Insn::GETLOCAL_WC_0 || $operationEntry->insn === Insn::GETLOCAL_WC_1) {
                ++$i;
                $number = $entries[$i]->operand->symbol->number;
                if (in_array($number, $ignoredIndexes, true)) {
                    continue;
                }
                if ($min === null || $min > $number) {
                    $min = $number;
                }
            }
        }

        if ($min !== null) {
            return $min;
        }

        return Option::VM_ENV_DATA_SIZE + $size;
    }
}
