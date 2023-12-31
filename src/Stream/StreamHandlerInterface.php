<?php

declare(strict_types=1);

namespace RubyVM\Stream;

interface StreamHandlerInterface
{
    public function read(int $bytes): string;

    public function readAll(): string;

    public function write(string $string): void;

    public function size(): ?int;

    public function isTerminated(): bool;

    /**
     * @return resource
     */
    public function resource();

    public function pos(int $newPos = null): int;
}
