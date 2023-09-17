<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Verification;

use RubyVM\VM\Core\Runtime\Verification\VerificationHeaderInterface;
use RubyVM\VM\Core\Runtime\Verification\VerificationInterface;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Kernel;
use RubyVM\VM\Exception\VerificationException;
use RubyVM\VM\Stream\SizeOf;

class VerificationHeader implements VerificationInterface
{
    public function __construct(protected readonly Kernel $kernel) {}

    private function verifyMagicByte(): void
    {
        if ('YARB' === $this->kernel->magic) {
            return;
        }

        $magicBytes = array_values(unpack('C*', $this->kernel->magic));

        throw new VerificationException(sprintf('The magic byte is not matched expecting YARB (actual: ' . implode(', ', array_fill(0, count($magicBytes), '0x%02x')) . ')', ...$magicBytes));
    }

    private function verifyVersion(): void
    {
        $actualVersion = "{$this->kernel->majorVersion}.{$this->kernel->minorVersion}";
        $expectedVersions = [];
        foreach ($this->kernel->expectedVersions() as $expectedRubyVersion) {
            $expectedVersion = $expectedRubyVersion->value;
            if (version_compare(
                $expectedVersion,
                $actualVersion,
                '==',
            )
            ) {
                return;
            }

            $expectedVersions[] = $expectedVersion;
        }

        throw new VerificationException(sprintf('The YARB structure is not matched expecting ruby version (expected: [%s], actual: %s)', implode(', ', $expectedVersions), $actualVersion));
    }

    private function verifyFileSize(): void
    {
        $size = $this->kernel->stream()->size();
        if ($size === null) {
            throw new VerificationException('The stream size is invalid (size: null)');
        }

        // NOTE: Append an EOF byte when comparing
        if ($size !== ($this->kernel->size + $this->kernel->extraSize + 1)) {
            throw new VerificationException(sprintf('The stream size is invalid (expected: %d, actual: %d)', $this->kernel->size, $size));
        }
    }

    private function verifyExtraSize(): void {}

    private function verifyInstructionSequenceListSize(): void
    {
        $size = $this->kernel->stream()->size();

        if ($this->kernel->instructionSequenceListSize > $size) {
            throw new VerificationException(sprintf('Overflowed the instruction sequence list size. Maybe the YARB structure was broken or unsupported.'));
        }
    }

    private function verifyGlobalObjectListSize(): void
    {
        $size = $this->kernel->stream()->size();

        if ($this->kernel->globalObjectListSize <= $size) {
            return;
        }

        throw new VerificationException(sprintf('Overflowed the global object list size. Maybe the YARB structure was broken or unsupported.'));
    }

    private function verifyInstructionListOffset(): void
    {
        $size = $this->kernel->stream()->size();

        if (($this->kernel->instructionSequenceListOffset + $this->kernel->instructionSequenceListSize) <= $size) {
            return;
        }

        throw new VerificationException(sprintf('Overflowed the instruction sequence list offset. Maybe the YARB structure was broken or unsupported.'));
    }

    private function verifyGlobalObjectListOffset(): void
    {
        $size = $this->kernel->stream()->size();

        if (($this->kernel->instructionSequenceListOffset + ($this->kernel->instructionSequenceListSize * SizeOf::UNSIGNED_LONG->size())) <= $size) {
            return;
        }

        throw new VerificationException(sprintf('Overflowed the global object list offset. Maybe the YARB structure was broken or unsupported.'));
    }

    public function verify(): bool
    {
        $this->verifyMagicByte();
        $this->verifyVersion();
        $this->verifyFileSize();
        $this->verifyExtraSize();
        $this->verifyGlobalObjectListOffset();
        $this->verifyGlobalObjectListSize();
        $this->verifyInstructionSequenceListSize();
        $this->verifyInstructionListOffset();

        return true;
    }

    public function verifierName(): string
    {
        return VerificationHeaderInterface::class;
    }
}
