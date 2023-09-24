<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Accessor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Entity\Array_;
use RubyVM\VM\Core\Runtime\Entity\Boolean_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Entity\Float_;
use RubyVM\VM\Core\Runtime\Entity\Number;
use RubyVM\VM\Core\Runtime\Entity\Range;
use RubyVM\VM\Core\Runtime\Entity\String_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\TranslationException;

readonly class Translator
{
    public static function PHPToRuby(mixed $elements): RubyClassInterface
    {
        if (is_array($elements)) {
            if (self::validateArrayIsNumber($elements)) {
                return (new Range(new RangeSymbol(
                    new NumberSymbol((int) array_key_first($elements)),
                    new NumberSymbol((int) array_key_last($elements)),
                    false,
                )))->toBeRubyClass();
            }

            $result = [];
            foreach ($elements as $element) {
                $result[] = self::PHPToRuby($element)->entity()->symbol();
            }

            return Array_::createBy($result)
                ->toBeRubyClass();
        }

        if (is_object($elements)) {
            throw new TranslationException('The RubyVM cannot use an object from the outside, only using scalar value');
        }

        return match (gettype($elements)) {
            'integer' => Number::createBy($elements)->toBeRubyClass(),
            'string' => String_::createBy($elements)->toBeRubyClass(),
            'double' => Float_::createBy($elements)->toBeRubyClass(),
            'boolean' => Boolean_::createBy($elements)->toBeRubyClass(),
            default => throw new TranslationException('The type is not implemented yet')
        };
    }

    /**
     * @param EntityInterface|mixed[]|RubyClassInterface|SymbolInterface $objectOrClass
     */
    public static function RubyToPHP(SymbolInterface|EntityInterface|RubyClassInterface|array $objectOrClass): mixed
    {
        if (is_array($objectOrClass)) {
            return array_map(
                static fn ($element) => static::RubyToPHP($element),
                $objectOrClass,
            );
        }

        $symbol = $objectOrClass;
        if ($objectOrClass instanceof EntityInterface) {
            $symbol = $objectOrClass->symbol();
        } elseif ($objectOrClass instanceof RubyClass) {
            $symbol = $objectOrClass->entity()->symbol();
        }

        return match ($symbol::class) {
            FloatSymbol::class,
            NumberSymbol::class,
            StringSymbol::class,
            BooleanSymbol::class => $symbol->valueOf(),
            RangeSymbol::class,
            ArraySymbol::class => array_map(
                static fn (SymbolInterface $element) => static::RubyToPHP($element),
                $symbol->valueOf(),
            ),
            NilSymbol::class => null,
            default => throw new TranslationException(
                sprintf(
                    'The type is not implemented yet (%s)',
                    ClassHelper::nameBy($symbol),
                ),
            ),
        };
    }

    public function __construct(public readonly RubyClass $object) {}

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
