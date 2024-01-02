<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Structure;

use RubyVM\Stream\BinaryStreamReaderInterface;
use RubyVM\Stream\SizeOf;

abstract class AbstractStructure implements StructureInterface
{
    /**
     * @var array<string, float|int|string>
     */
    private array $structureProperties = [];

    public function __construct(protected readonly BinaryStreamReaderInterface $reader)
    {
        /**
         * @var string $name
         * @var SizeOf $sizeOf
         */
        foreach (static::structure() as $name => $sizeOf) {
            $this->structureProperties[$name] = match ($sizeOf) {
                SizeOf::CHAR => $this->reader->readAsChar(),
                SizeOf::BYTE => $this->reader->readAsByte(),
                SizeOf::SHORT => $this->reader->readAsShort(),
                SizeOf::INT => $this->reader->readAsInt(),
                SizeOf::LONG => $this->reader->readAsLong(),
                SizeOf::LONG_LONG => $this->reader->readAsLongLong(),
                SizeOf::UNSIGNED_BYTE => $this->reader->readAsUnsignedByte(),
                SizeOf::UNSIGNED_SHORT => $this->reader->readAsUnsignedShort(),
                SizeOf::UNSIGNED_INT => $this->reader->readAsUnsignedInt(),
                SizeOf::UNSIGNED_LONG => $this->reader->readAsUnsignedLong(),
                SizeOf::UNSIGNED_LONG_LONG => $this->reader->readAsUnsignedLongLong(),
                default => $this->reader->read($sizeOf->size()),
            };
        }
    }

    public function __get(string $name): float|int|string
    {
        return $this->structureProperties[$name];
    }
}
