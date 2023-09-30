<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Class_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;
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

        $symbol = $operand->object;

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
                // @phpstan-ignore-next-line
                $object = $className::createBy();
            } elseif ($constantNameSymbol instanceof RubyClassInterface) {
                $object = Class_::of($constantNameSymbol->symbol(), $this->context);
            } else {
                $object = Class_::of($constantNameSymbol, $this->context);
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
                ->setUserlandHeapSpace($object->userlandHeapSpace());

            $this->context->vmStack()->push(new Operand($object));
        }

        return ProcessedStatus::SUCCESS;
    }
}
