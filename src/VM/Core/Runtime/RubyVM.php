<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyVMInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Verification\VerificationHeaderInterface;
use RubyVM\VM\Core\Runtime\Verification\Verifier;
use RubyVM\VM\Core\YARV\RubyVersion;
use RubyVM\VM\Exception\RubyVMException;

class RubyVM implements RubyVMInterface
{
    public const DEFAULT_VERSION = RubyVersion::VERSION_3_2;

    protected array $registeredRuntimes = [];

    public function __construct(public readonly Option $option)
    {
        // Register default kernels
        $this->register(
            rubyVersion: RubyVersion::VERSION_3_2,
            kernelClass: \RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Kernel::class,
        );
    }

    /**
     * NOTE: You did call register method to be overwrite default kernel if you want to replace any kernel.
     *
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
            sprintf('Start to disassemble instruction sequences'),
        );

        $runtime = $this->runtime($useVersion ?? static::DEFAULT_VERSION);

        $kernel = $runtime->kernel()->setup();

        $this->option->logger->info(
            sprintf('Complete to disassemble instruction sequences'),
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

        return Executor::createEntryPoint(
            $kernel,
            $this->option,
        );
    }

    public function runtime(RubyVersion $useVersion = null): Runtime
    {
        $selectedVersion = null;

        // @var Runtime|null $kernel
        if ($useVersion === null) {
            $runtime = $this->registeredRuntimes[$selectedVersion = array_key_first($this->registeredRuntimes)] ?? null;
        } else {
            $runtime = $this->registeredRuntimes[$selectedVersion = $useVersion->value] ?? null;
        }

        if ($runtime === null) {
            throw new RubyVMException('The RubyVM is not registered a kernel - You should call RubyVM::register method before calling the disassemble method');
        }

        $this->option->logger->info(
            sprintf('Selected Ruby %s version kernel', $selectedVersion),
        );

        return $runtime;
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
