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

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2;

use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorEntries;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\InstructionSequence\InstructionSequenceProcessor;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceProcessorInterface;
use RubyVM\VM\Core\YARV\RubyVersion;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Kernel as BaseKernel;

class Kernel extends BaseKernel implements KernelInterface
{
    protected string $rubyPlatform;

    protected function setupHeaders(): KernelInterface
    {
        parent::setupHeaders();

        $this->size = $this->stream()->readAsUnsignedLong();
        $this->extraSize = $this->stream()->readAsUnsignedLong();
        $this->instructionSequenceListSize = $this->stream()->readAsUnsignedLong();
        $this->globalObjectListSize = $this->stream()->readAsUnsignedLong();
        $this->instructionSequenceListOffset = $this->stream()->readAsUnsignedLong();
        $this->globalObjectListOffset = $this->stream()->readAsUnsignedLong();
        $this->rubyPlatform = $this->stream()->readAsString();

        return $this;
    }

    public function rubyPlatform(): ?string
    {
        return $this->rubyPlatform;
    }

    public function expectedVersions(): array
    {
        return [RubyVersion::VERSION_3_2];
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

        return $entries ??= new \RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\InstructionSequence\OperationProcessorEntries();
    }
}
