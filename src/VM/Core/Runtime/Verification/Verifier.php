<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Verification;

use RubyVM\VM\Exception\VerifierException;

class Verifier
{
    /**
     * @var array<string, bool>
     */
    private array $verified = [];

    /**
     * @param string[] $requireVerificationNames
     */
    public function __construct(
        array $requireVerificationNames
    ) {
        foreach ($requireVerificationNames as $requireVerificationName) {
            $this->verified[$requireVerificationName] = false;
        }
    }

    public function isVerified(string $verifyName): bool
    {
        return $this->verified[$verifyName];
    }

    public function verify(VerificationInterface $verification): bool
    {
        $verify = $verification->verify();
        if ($verify) {
            $this->verified[$verification->verifierName()] = true;
        }

        return $verify;
    }

    public function done(): void
    {
        foreach ($this->verified as $verifierName => $isVerified) {
            if ($isVerified) {
                continue;
            }

            throw new VerifierException(sprintf('The %s is not verified. The verifier requires verification to be all done', $verifierName));
        }
    }
}
