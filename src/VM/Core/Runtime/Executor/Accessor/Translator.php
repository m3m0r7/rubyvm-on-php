<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Accessor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\RubyClassInterface;
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
    public static function PHPToRuby(mixed $elements): RubyClass
    {
        if (is_array($elements)) {
            if (static::validateArrayIsNumber($elements)) {
                return (new RangeSymbol(
                    new NumberSymbol((int) array_key_first($elements)),
                    new NumberSymbol((int) array_key_last($elements)),
                    false,
                ))->toRubyClass();
            }
            $result = [];
            foreach ($elements as $element) {
                $result[] = static::PHPToRuby($element)->symbol;
            }

            return (new ArraySymbol($result))
                ->toRubyClass();
        }

        if (is_object($elements)) {
            throw new TranslationException('The RubyVM cannot use an object from the outside, only using scalar value');
        }

        return match (gettype($elements)) {
            'integer' => (new NumberSymbol($elements))->toRubyClass(),
            'string' => (new StringSymbol($elements))->toRubyClass(),
            'double' => (new FloatSymbol($elements))->toRubyClass(),
            'boolean' => (new BooleanSymbol($elements))->toRubyClass(),
            default => throw new TranslationException('The type is not implemented yet')
        };
    }

    public static function RubyToPHP(RubyClass|SymbolInterface|RubyClassInterface|array $objectOrClass): mixed
    {
        if (is_array($objectOrClass)) {
            return array_map(
                fn ($element) => static::RubyToPHP($element),
                $objectOrClass,
            );
        }

        if ($objectOrClass instanceof RubyClass || $objectOrClass instanceof SymbolInterface) {
            if ($objectOrClass instanceof RubyClass) {
                $symbol = $objectOrClass->symbol;
            } else {
                $symbol = $objectOrClass;
            }

            return match ($symbol::class) {
                FloatSymbol::class,
                NumberSymbol::class,
                StringSymbol::class,
                BooleanSymbol::class => $symbol->valueOf(),
                RangeSymbol::class,
                ArraySymbol::class => array_map(
                    fn (SymbolInterface $element) => static::RubyToPHP($element),
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

        return $objectOrClass;
    }

    public function __construct(public readonly RubyClass $object) {}

    public function toOperand(): OperandEntry
    {
        return new OperandEntry($this->object);
    }

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
