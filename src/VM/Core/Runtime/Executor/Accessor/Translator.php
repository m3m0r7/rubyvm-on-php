<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Accessor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Float_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Array_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Range;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\FalseClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\TrueClass;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\TranslationException;

readonly class Translator
{
    public static function PHPToRuby(mixed $elements): RubyClassInterface
    {
        if (is_array($elements)) {
            if (self::validateArrayIsNumber($elements)) {
                return new Range(new RangeSymbol(
                    new NumberSymbol((int) array_key_first($elements)),
                    new NumberSymbol((int) array_key_last($elements)),
                    false,
                ));
            }

            $result = [];
            foreach ($elements as $element) {
                $result[] = self::PHPToRuby($element);
            }

            return Array_::createBy($result)
            ;
        }

        if (is_object($elements)) {
            throw new TranslationException('The RubyVM cannot use an object from the outside, only using scalar value');
        }

        return match (gettype($elements)) {
            'integer' => Integer_::createBy($elements),
            'string' => String_::createBy($elements),
            'double' => Float_::createBy($elements),
            'boolean' => $elements
                ? TrueClass::createBy()
                : FalseClass::createBy(),
            default => throw new TranslationException('The type is not implemented yet')
        };
    }

    /**
     * @param (RubyClassInterface|SymbolInterface)[]|RubyClassInterface $objectOrClass
     */
    public static function RubyToPHP(RubyClassInterface|SymbolInterface|array $objectOrClass): mixed
    {
        if (is_array($objectOrClass)) {
            return array_map(
                static fn ($element) => static::RubyToPHP($element),
                $objectOrClass,
            );
        }

        if (is_array($objectOrClass->valueOf())) {
            return array_map(
                static fn ($element) => static::RubyToPHP($element),
                $objectOrClass->valueOf(),
            );
        }

        return $objectOrClass->valueOf();
    }

    public function __construct(public readonly RubyClassInterface $object) {}

    public function toOperand(): Operand
    {
        return new Operand($this->object);
    }

    /**
     * @param mixed[] $values
     */
    private static function validateArrayIsNumber(array $values): bool
    {
        if (!array_is_list($values)) {
            return false;
        }

        // FIXME: The fix steps is fixed
        $steps = 1;

        for ($i = 1; $i < count($values); ++$i) {
            if (($values[$i] - $values[$i - 1]) !== $steps) {
                return false;
            }
        }

        return true;
    }
}
