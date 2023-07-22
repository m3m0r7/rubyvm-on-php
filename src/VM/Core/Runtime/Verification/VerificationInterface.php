<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Verification;

interface VerificationInterface
{
    public function verify(): bool;

    public function verifierName(): string;
}
