<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn;

interface InsnInterface
{
    public static function of(int $value): self;

    public static function find(string $name): self;

    public function name(): string;

    public function value(): int;
}
