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

        $this->option->logger->info(
            sprintf('Registered Ruby version %s kernel', $rubyVersion->value)
        );

        return $this;
    }

    public function disassemble(RubyVersion $useVersion = null): ExecutorInterface
    {
        $this->option->logger->info(
            sprintf('Start to disassemble an instruction sequence'),
        );

        $selectedVersion = null;
        /**
         * @var Runtime|null $kernel
         */
        if ($useVersion === null) {
            $runtime = $this->registeredRuntimes[$selectedVersion = array_key_first($this->registeredRuntimes)] ?? null;
        } else {
            $runtime = $this->registeredRuntimes[$selectedVersion = $useVersion->value] ?? null;
        }

        if ($runtime === null) {
            throw new RubyVMException(
                'The RubyVM is not registered a kernel - You should call RubyVM::register method before calling the disassemble method'
            );
        }

        $this->option->logger->info(
            sprintf('Selected Ruby %s version kernel', $selectedVersion),
        );

        $executor = $runtime
            ->kernel
            ->setup()
            ->process();

        $this->option->logger->info(
            sprintf('Complete to disassemble an instruction sequence'),
        );

        $this->option->logger->info(
            sprintf('Check to verify process for an instruction sequence structure'),
        );

        // Verify structures
        $runtime
            ->verifier
            ->done();

        $this->option->logger->info(
            sprintf('Complete to verify process'),
        );

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
