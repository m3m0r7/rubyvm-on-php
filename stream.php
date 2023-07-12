<?php
enum Endian
{
    case LITTLE_ENDIAN;
    case BIG_ENDIAN;
}

class SizeOf
{
    const BOOL = 1;
    const CHAR = 1;
    const BYTE = 1;
    const SHORT = 2;
    const LONG = 4;
    const LONG_LONG = 8;
}

class ReadException extends RuntimeException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = 'Cannot read with ' . $message . ' in the stream';
        parent::__construct($message, $code, $previous);
    }
}

class Stream
{
    protected readonly mixed $handle;

    public function __construct(public readonly string $path, protected Endian $endian = Endian::LITTLE_ENDIAN)
    {
        $this->handle = fopen($path, 'r');
    }

    public function switchEndian(Endian $newEndian): void
    {
        $this->endian = $newEndian;
    }

    public function read(int $bytes): string
    {
        if ($bytes === 0) {
            return '';
        }
        $read = fread($this->handle, $bytes);
        $readSize = strlen($read);
        if ($readSize !== $bytes) {
            throw new RuntimeException('Not enough reading ' . abs($bytes - $readSize) . ' byte sizes');
        }
        if ($read === false) {
            return '';
        }
        return $read;
    }

    public function readUnsignedShort(): int
    {
        return $this->readWithEndian(
            __FUNCTION__,
            'v',
            'n',
            SizeOf::SHORT,
        );
    }

    public function readLong(): int
    {
        $bytes = array_values(unpack('C*', $this->read(SizeOf::LONG)));
        if ($this->endian === Endian::LITTLE_ENDIAN) {
            // Fixed 4 bytes
            return ($bytes[3] << 24) + ($bytes[2] << 16) + ($bytes[1] << 8) + $bytes[0];
        }

        return ($bytes[0] << 24) + ($bytes[1] << 16) + ($bytes[2] << 8) + $bytes[3];
    }

    public function readUnsignedLong(): int
    {
        return $this->readWithEndian(
            __FUNCTION__,
            'V',
            'N',
            SizeOf::LONG,
        );
    }

    public function readLongLong(): int
    {
        $bytes = array_values(unpack('C*', $this->read(SizeOf::LONG_LONG)));
        if ($this->endian === Endian::LITTLE_ENDIAN) {
            return (($bytes[7] << 56) + ($bytes[6] << 48) + ($bytes[5] << 40) + ($bytes[4] << 32)) // high
                + (($bytes[3] << 24) + ($bytes[2] << 16) + ($bytes[1] << 8) + $bytes[0]); // low
        }

        return (($bytes[0] << 56) + ($bytes[1] << 48) + ($bytes[2] << 40) + ($bytes[3] << 32)) // high
            + (($bytes[4] << 24) + ($bytes[5] << 16) + ($bytes[6] << 8) + $bytes[7]); // low
    }

    public function readUnsignedLongLong(): int
    {
        return $this->readWithEndian(
            __FUNCTION__,
            'P',
            'J',
            SizeOf::LONG_LONG
        );
    }

    public function readUnsignedChar(): string
    {
        return chr($this->readUnsignedChar());
    }

    public function readChar(): string
    {
        return chr($this->readByte());
    }

    public function readUnsignedByte(): int
    {
        return $this->readWithEndian(
            __FUNCTION__,
            'C',
            'C',
            SizeOf::BYTE,
        );
    }

    public function readByte(): int
    {
        return $this->readWithEndian(
            __FUNCTION__,
            'c',
            'c',
            SizeOf::BYTE,
        );
    }

    public function move(int $bytes, int $whence = SEEK_SET): void
    {
        fseek(
            $this->handle,
            $bytes,
            $whence,
        );
    }

    public function moveNext(): void
    {
        $this->move(1, SEEK_CUR);
    }

    public function pos(): int
    {
        $pos = ftell($this->handle);
        if ($pos === false) {
            throw new RuntimeException('Cannot move cursor to next in the stream');
        }
        return $pos;
    }

    public function value(int $pos, int $readSize = SizeOf::BYTE): int
    {
        $currentPos = $this->pos();
        $read = match($readSize) {
            SizeOf::BYTE => $this->readUnsignedByte(),
            SizeOf::LONG => $this->readUnsignedLong(),
            SizeOf::LONG_LONG => $this->readUnsignedLongLong(),
            default => throw new RuntimeException('Unknown read size'),
        };
        $this->move($currentPos);
        return $read;
    }

    private function readWithEndian(string $functionName, string $littleEndian, string $bigEndian, int $bytes): mixed
    {
        $read = unpack(
            $this->endian === Endian::LITTLE_ENDIAN
                ? $littleEndian
                : $bigEndian,
            $this->read($bytes),
        );
        if ($read === false) {
            throw new ReadException($functionName);
        }
        return $read[array_key_first($read)];
    }
}
