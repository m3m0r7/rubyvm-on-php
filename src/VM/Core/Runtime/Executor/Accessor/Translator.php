<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Accessor;

use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\BooleanSymbol;
use RubyVM\VM\Core\Runtime\Symbol\FloatSymbol;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\RangeSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\TranslationException;

class Translator
{
    public static function PHPToRuby(mixed $elements): self
    {
        if (is_array($elements)) {
            if (static::validateArrayIsNumber($elements)) {
                return new self(new RangeSymbol(
                    new NumberSymbol((int) array_key_first($elements)),
                    new NumberSymbol((int) array_key_last($elements)),
                    false,
                ));
            }
            $result = [];
            foreach ($elements as $element) {
                $result[] = static::PHPToRuby($element)
                    ->symbol
                ;
            }

            return new self(new ArraySymbol($result));
        }

        if (is_object($elements)) {
            throw new TranslationException('The RubyVM cannot use an object from the outside, only using scalar value');
        }

        return new self(match (gettype($elements)) {
            'integer' => (new NumberSymbol($elements)),
            'string' => (new StringSymbol($elements)),
            'double' => (new FloatSymbol($elements)),
            'boolean' => (new BooleanSymbol($elements)),
            default => throw new TranslationException('The type is not implemented yet')
        });
    }

    public static function RubyToPHP(SymbolInterface $symbol): mixed
    {
        return match ($symbol::class) {
            StringSymbol::class => $symbol->string,
            BooleanSymbol::class => $symbol->boolean,
            RangeSymbol::class,
            ArraySymbol::class => array_map(
                fn (SymbolInterface $element) => static::RubyToPHP($element),
                $symbol->array,
            ),
            FloatSymbol::class,
            NumberSymbol::class => $symbol->number,
            default => throw new TranslationException('The type is not implemented yet'),
        };
    }

    public function __construct(public readonly SymbolInterface $symbol)
    {
    }

    public function toOperand(): OperandEntry
    {
        return new OperandEntry($this->symbol->toObject());
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
