<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Structure;

use RubyVM\VM\Exception\RubyVMException;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;
use RubyVM\VM\Stream\SizeOf;

abstract class AbstractStructure implements StructureInterface
{
    private array $structureProperties = [];

    public function __construct(protected readonly BinaryStreamReaderInterface $reader)
    {
        /**
         * @var string $name
         * @var SizeOf $sizeOf
         */
        foreach (static::structure() as $name => $sizeOf) {
            if (!($sizeOf instanceof SizeOf) && !is_int($sizeOf)) {
                throw new RubyVMException('The AbstractStructure::structure accepts processing instantiated by SizeOf or integer property');
            }
            $this->structureProperties[$name] = match ($sizeOf) {
                SizeOf::CHAR => $this->readerreadAsChar(),
                SizeOf::BYTE => $this->reader->byte(),
                SizeOf::SHORT => $this->reader->short(),
                SizeOf::INT => $this->reader->readAsInt(),
                SizeOf::LONG => $this->reader->long(),
                SizeOf::LONG_LONG => $this->reader->longLong(),
                SizeOf::UNSIGNED_BYTE => $this->reader->unsignedByte(),
                SizeOf::UNSIGNED_SHORT => $this->reader->unsignedShort(),
                SizeOf::UNSIGNED_INT => $this->reader->unsignedInt(),
                SizeOf::UNSIGNED_LONG => $this->reader->readAsUnsignedLong(),
                SizeOf::UNSIGNED_LONG_LONG => $this->reader->readAsUnsignedLongLong(),
                default => $this->reader->read($sizeOf),
            };
        }
    }

    public function __get(string $name): int|string
    {
        return $this->structureProperties[$name];
    }
}
