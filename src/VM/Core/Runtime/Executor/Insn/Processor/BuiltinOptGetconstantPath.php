<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\ClassSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;
use RubyVM\VM\Core\YARV\Criterion\Essential\RubyClassInterface;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinOptGetconstantPath implements OperationProcessorInterface
{
    use OperandHelper;
    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        $operand = $this->getOperandAsID();

        /**
         * @var ArraySymbol $symbol
         */
        $symbol = $operand->object->symbol;

        /**
         * @var StringSymbol $constantNameSymbol
         */
        foreach ($symbol->valueOf() as $constantNameSymbol) {
            $classes = $this->context->self()->userlandHeapSpace()->userlandClasses();

            $className = $constantNameSymbol->valueOf();

            if (!class_exists($className)) {
                $className = $classes->aliasNameBy($constantNameSymbol->valueOf());
            }

            // Check if already aliased class
            if ($className) {
                if (!class_exists($className)) {
                    throw new OperationProcessorException(
                        sprintf(
                            'The class was not found: %s',
                            $className,
                        ),
                    );
                }

                $object = Object_::initializeByClassName($className);
            } else {
                $object = (new ClassSymbol($constantNameSymbol))
                    ->toObject();
            }

            $heapSpace = $classes->get($constantNameSymbol->valueOf());

            if ($heapSpace === null) {
                $this->context
                    ->self()
                    ->userlandHeapSpace()
                    ->userlandClasses()
                    ->set(
                        $constantNameSymbol->valueOf(),
                        $heapSpace = new UserlandHeapSpace()
                    );
            }

            $object->setRuntimeContext($this->context)
                ->setUserlandHeapSpace($heapSpace);

            $this->context->vmStack()->push(new OperandEntry($object));
        }

        return ProcessedStatus::SUCCESS;
    }
}
