<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\NilSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\RangeSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\RubyVMException;

trait ProvideBasicClassMethods
{
    /**
     * @var Object_[]
     */
    protected array $instanceVariables = [];

    public function setInstanceVariable(ID $id, Object_ $object): void
    {
        $this->instanceVariables[(string) $id->object->symbol] = $object;
    }

    public function getInstanceVariable(ID $id): Object_
    {
        $key = (string) $id->object->symbol;
        if (!isset($this->instanceVariables[$key])) {
            throw new RubyVMException(
                sprintf(
                    'The ref %s is not found on %s',
                    $key,
                    ClassHelper::nameBy($this),
                )
            );
        }

        return $this->instanceVariables[$key];
    }

    public function puts(SymbolInterface $symbol): SymbolInterface
    {
        $string = '';
        if ($symbol instanceof ArraySymbol || $symbol instanceof RangeSymbol) {
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

        $this->kernel->IOContext()->stdOut->write($string);

        // The puts returns (nil)
        return new NilSymbol();
    }

    public function exit(int $code = 0): void
    {
        exit($code);
    }
}
