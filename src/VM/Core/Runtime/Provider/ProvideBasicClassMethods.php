<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Exception;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Lambda;
use RubyVM\VM\Core\Runtime\Entity\Nil;
use RubyVM\VM\Core\Runtime\Entity\String_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CatchInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolSymbol;
use RubyVM\VM\Exception\Raise;

trait ProvideBasicClassMethods
{
    public function puts(CallInfoInterface $callInfo, RubyClassInterface $object): RubyClassInterface
    {
        $symbol = $object->entity()->symbol();

        $string = '';
        if ($object instanceof Exception) {
            $string .= (string) $object;
        } elseif ($symbol instanceof ArraySymbol || $symbol instanceof RangeSymbol) {
            foreach ($symbol as $number) {
                $string .= "{$number}\n";
            }
        } elseif ($symbol instanceof NilSymbol) {
            // When an argument is a nil symbol, then displays a break only
            $string = "\n";
        } else {
            $string = (string) $symbol;
        }

        if (!str_ends_with($string, "\n")) {
            $string .= "\n";
        }

        $this->context()->IOContext()->stdOut->write($string);

        // The puts returns (nil)
        return Nil::createBy()
            ->toBeRubyClass();
    }

    public function exit(CallInfoInterface $callInfo, int $code = 0): never
    {
        exit($code);
    }

    public function inspect(): RubyClassInterface
    {
        $string = match (($this->entity()->symbol())::class) {
            StringSymbol::class => '"' . ((string) $this) . '"',
            default => (string) $this,
        };

        return String_::createBy($string)
            ->toBeRubyClass();
    }

    public function lambda(CallInfoInterface $callInfo, ContextInterface $context): RubyClassInterface
    {
        return (new Lambda($context->instructionSequence()))
            ->setRuntimeContext($this->context())
            ->setUserlandHeapSpace(new UserlandHeapSpace());
    }

    public function raise(CallInfoInterface $callInfo, RubyClassInterface $string, RubyClassInterface $class): RubyClassInterface
    {
        assert($class instanceof Exception);

        $pos = $this
            ->context()
            ->programCounter()
            ->pos();

        $lookedUpCatchEntry = null;

        /**
         * @var CatchInterface $entry
         */
        foreach ($this->context()->instructionSequence()->body()->info()->catchEntries() as $entry) {
            if ($pos >= $entry->start() && $entry->end() > $pos) {
                /**
                 * @var Nil|SymbolSymbol[] $targetClasses
                 */
                $targetClasses = $this
                    ->context()
                    ->kernel()
                    ->findObject($entry->cont());

                if ($targetClasses instanceof Nil) {
                    return Nil::createBy()
                        ->toBeRubyClass();
                }

                foreach ($targetClasses as $targetClass) {
                    if ($targetClass->valueOf() === $class->entity()->valueOf()) {
                        $lookedUpCatchEntry = $entry;
                    }

                    if ($lookedUpCatchEntry !== null) {
                        break;
                    }
                }

                if ($lookedUpCatchEntry !== null) {
                    break;
                }
            }
        }

        if ($lookedUpCatchEntry === null) {
            throw new Raise("{$class->entity()->valueOf()}: {$string->entity()->valueOf()}");
        }

        $instructionSequence = $lookedUpCatchEntry
            ->instructionSequence();

        $executor = new Executor(
            kernel: $this->context()->kernel(),
            rubyClass: $this->context()->self(),
            instructionSequence: $instructionSequence,
            option: $this->context()->option(),
            debugger: $this->context()->debugger(),
            parentContext: $this->context()->parentContext(),
        );

        $executor->context()
            ->renewEnvironmentTable();

        $tableSize = $lookedUpCatchEntry
            ->instructionSequence()
            ->body()
            ->info()
            ->localTableSize();

        $executor->context()
            ->environmentTable()
            ->set(
                Option::VM_ENV_DATA_SIZE + $tableSize - 1,
                $class->setMessage($string),
            );

        $result = $executor->execute();

        if ($result->threw instanceof \Throwable) {
            throw $result->threw;
        }

        if (!$result->returnValue instanceof \RubyVM\VM\Core\Runtime\Essential\RubyClassInterface) {
            return Nil::createBy()
                ->toBeRubyClass();
        }

        return $result->returnValue;
    }
}
