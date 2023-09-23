<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\EntityInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\OperationProcessorContext;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

class Range extends Entity implements EntityInterface
{
    public function __construct(RangeSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function each(OperationProcessorContext $context): SymbolInterface
    {
        foreach ($this->symbol->valueOf() as $index => $number) {
            $executor = (new Executor(
                kernel: $context->kernel(),
                rubyClass: $context->self(),
                instructionSequence: $context->instructionSequence(),
                option: $context->option(),
                debugger: $context->debugger(),
                previousContext: $context,
            ));

            $executor->context()
                ->appendTrace(ClassHelper::nameBy($this) . '#' . __FUNCTION__);

            $localTableSize = $executor->context()->instructionSequence()->body()->data->localTableSize();
            $object = (new Number($number))
                ->toRubyClass()
                ->setRuntimeContext($context)
                ->setUserlandHeapSpace($context->self()->userlandHeapSpace());

            $executor->context()
                ->environmentTable()
                ->set(
                    LocalTableHelper::computeLocalTableIndex(
                        $localTableSize,
                        Option::VM_ENV_DATA_SIZE + $localTableSize - 1,
                    ),
                    $object,
                );

            $result = $executor->execute();

            // An occurred exception to be throwing
            if ($result->threw) {
                throw $result->threw;
            }
        }

        return new NilSymbol();
    }
}
