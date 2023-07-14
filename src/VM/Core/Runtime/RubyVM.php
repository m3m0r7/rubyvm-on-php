<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Verification\VerificationHeaderInterface;
use RubyVM\VM\Core\Runtime\Verification\Verifier;
use RubyVM\VM\Exception\RubyVMException;

class RubyVM implements RubyVMInterface
{
    const RUBY_ENCINDEX_BUILTIN_MAX = 12;

    protected array $registeredRuntimes = [];

    public function __construct(public readonly Option $option)
    {
    }

    /**
     * @param class-string<KernelInterface> $kernelClass
     */
    public function register(
        RubyVersion $rubyVersion,
        string $kernelClass,
    ): self {
        $verifier = new Verifier(
            $this->requireVerifications(),
        );
        $this->registeredRuntimes[$rubyVersion->value] = new Runtime(
            new $kernelClass(
                $this,
                $verifier,
            ),
            $verifier,
        );

        return $this;
    }

    public function disassemble(RubyVersion $useVersion): ExecutorInterface
    {
        /**
         * @var Runtime|null $kernel
         */
        $runtime = $this->registeredRuntimes[$useVersion->value] ?? null;

        if ($runtime === null) {
            throw new RubyVMException(
                'The RubyVM is not registered a kernel - You should call RubyVM::register method before calling the disassemble method'
            );
        }

        $executor = $runtime
            ->kernel
            ->setup()
            ->process();

        // Verify structures
        $runtime
            ->verifier
            ->done();

        return $executor;
    }

    public function option(): Option
    {
        return $this->option;
    }

    public function requireVerifications(): array
    {
        return [
            VerificationHeaderInterface::class,
        ];
    }
}
