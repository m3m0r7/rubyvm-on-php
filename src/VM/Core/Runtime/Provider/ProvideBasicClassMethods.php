<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Runtime\Attribute\WithContext;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Comparable;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Enumerable;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Exception;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Proc;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CatchInterface;
use RubyVM\VM\Exception\ExitException;
use RubyVM\VM\Exception\Raise;

trait ProvideBasicClassMethods
{
    public function p(RubyClassInterface $object): RubyClassInterface
    {
        return $this->puts($object->inspect());
    }

    public function puts(RubyClassInterface $object): RubyClassInterface
    {
        $string = '';

        if ($object instanceof Exception) {
            $string .= (string) $object;
        } elseif ($object instanceof Enumerable) {
            foreach ($object as $number) {
                $string .= "{$number}\n";
            }
        } elseif ($object instanceof NilClass) {
            // When an argument is a nil symbol, then displays a break only
            $string = "\n";
        } elseif ($object instanceof Comparable) {
            $string = (string) $object;
        } else {
            $string = (string) $object->setRuntimeContext($this->context())->inspect();
        }

        if (!str_ends_with($string, "\n")) {
            $string .= "\n";
        }

        $this->context()->IOContext()->stdOut->write($string);

        // The puts returns (nil)
        return NilClass::createBy();
    }

    public function exit(RubyClassInterface $code = null): never
    {
        if ($code instanceof Integer_) {
            throw new ExitException($code->valueOf());
        }

        throw new ExitException(0);
    }

    public function inspect(): RubyClassInterface
    {
        return String_::createBy(
            sprintf(
                '#<%s:0x%s %s:%d>',
                $this->className(),
                spl_object_hash($this),
                $this->context
                    ? $this->context()
                        ->instructionSequence()
                        ->body()
                        ->info()
                        ->path()
                    : 'unknown',
                -1
            )
        );
    }

    #[WithContext]
    public function lambda(ContextInterface $context): RubyClassInterface
    {
        return (new Proc($context->instructionSequence()))
            ->setRuntimeContext($this->context())
            ->setUserlandHeapSpace(new UserlandHeapSpace());
    }

    public function raise(RubyClassInterface $string, RubyClassInterface $class): RubyClassInterface
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
                $lookedUpCatchEntry = $entry;

                break;
            }
        }

        if ($lookedUpCatchEntry === null) {
            throw new Raise(
                $this->context(),
                $class->valueOf(),
                $string->valueOf(),
            );
        }

        $instructionSequence = $lookedUpCatchEntry
            ->instructionSequence();

        $executor = new Executor(
            kernel: $this->context()->kernel(),
            rubyClass: $this->context()->self(),
            instructionSequence: $instructionSequence,
            option: $this->context()->option(),
            parentContext: $this->context(),
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
            return NilClass::createBy();
        }

        return $result->returnValue;
    }
}
