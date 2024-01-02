<?php
/**
 * $$$$$$$\            $$\                 $$\    $$\ $$\      $$\                                 $$$$$$$\  $$\   $$\ $$$$$$$\
 * $$  __$$\           $$ |                $$ |   $$ |$$$\    $$$ |                                $$  __$$\ $$ |  $$ |$$  __$$\
 * $$ |  $$ |$$\   $$\ $$$$$$$\  $$\   $$\ $$ |   $$ |$$$$\  $$$$ |       $$$$$$\  $$$$$$$\        $$ |  $$ |$$ |  $$ |$$ |  $$ |
 * $$$$$$$  |$$ |  $$ |$$  __$$\ $$ |  $$ |\$$\  $$  |$$\$$\$$ $$ |      $$  __$$\ $$  __$$\       $$$$$$$  |$$$$$$$$ |$$$$$$$  |
 * $$  __$$< $$ |  $$ |$$ |  $$ |$$ |  $$ | \$$\$$  / $$ \$$$  $$ |      $$ /  $$ |$$ |  $$ |      $$  ____/ $$  __$$ |$$  ____/
 * $$ |  $$ |$$ |  $$ |$$ |  $$ |$$ |  $$ |  \$$$  /  $$ |\$  /$$ |      $$ |  $$ |$$ |  $$ |      $$ |      $$ |  $$ |$$ |
 * $$ |  $$ |\$$$$$$  |$$$$$$$  |\$$$$$$$ |   \$  /   $$ | \_/ $$ |      \$$$$$$  |$$ |  $$ |      $$ |      $$ |  $$ |$$ |
 * \__|  \__| \______/ \_______/  \____$$ |    \_/    \__|     \__|       \______/ \__|  \__|      \__|      \__|  \__|\__|
 *                               $$\   $$ |
 *                               \$$$$$$  |
 *                                \______/.
 */

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_3;

use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorEntries;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_3\InstructionSequence\InstructionSequenceProcessor;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceProcessorInterface;
use RubyVM\VM\Core\YARV\RubyVersion;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Kernel as BaseKernel;
use RubyVM\VM\Exception\UnknownEndianException;
use RubyVM\Stream\Endian;

class Kernel extends BaseKernel implements KernelInterface
{
    protected int $endian;

    protected int $wordSize;

    protected function setupHeaders(): KernelInterface
    {
        parent::setupHeaders();

        $this->endian = $this->stream()->readAsUnsignedShort();
        $this->wordSize = $this->stream()->readAsUnsignedShort();

        return $this;
    }

    public function endian(): Endian
    {
        return match ($value = chr($this->endian)) {
            'b' => Endian::BIG_ENDIAN,
            'l' => Endian::LITTLE_ENDIAN,
            default => throw new UnknownEndianException(
                sprintf('Unknown endian type `%s`', $value),
            ),
        };
    }

    public function wordSize(): int
    {
        return $this->wordSize;
    }

    public function expectedVersions(): array
    {
        return [RubyVersion::VERSION_3_3];
    }

    protected function createInstructionSequenceProcessor(Aux $aux): InstructionSequenceProcessorInterface
    {
        return new InstructionSequenceProcessor(
            $this,
            $aux,
        );
    }

    public function operationProcessorEntries(): OperationProcessorEntries
    {
        static $entries;

        return $entries ??= new \RubyVM\VM\Core\Runtime\Kernel\Ruby3_3\InstructionSequence\OperationProcessorEntries();
    }
}
