<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2;

use RubyVM\VM\Core\Helper\DefaultOperationProcessorEntries;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequences;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Offset\Offsets;
use RubyVM\VM\Core\Runtime\RubyVersion;
use RubyVM\VM\Core\Runtime\RubyVMInterface;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\LoaderInterface;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\ObjectInfo;
use RubyVM\VM\Core\Runtime\Symbol\SymbolType;
use RubyVM\VM\Core\Runtime\Verification\Verifier;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence\InstructionSequenceProcessor;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\FalseLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\FixedNumberLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\FloatLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\StringLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\SymbolLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\TrueLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard\Main;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Verification\VerificationHeader;
use RubyVM\VM\Exception\ResolverException;
use RubyVM\VM\Exception\RubyVMException;
use RubyVM\VM\Stream\BinaryStreamReader;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;
use RubyVM\VM\Stream\StreamHandler;

class Kernel implements KernelInterface
{
    public readonly string $magic;
    public readonly int $majorVersion;
    public readonly int $minorVersion;
    public readonly int $size;
    public readonly int $extraSize;
    public readonly int $instructionSequenceListSize;
    public readonly int $globalObjectListSize;
    public readonly int $instructionSequenceListOffset;
    public readonly int $globalObjectListOffset;
    public readonly Offsets $instructionSequenceList;
    public readonly Offsets $globalObjectList;
    protected readonly InstructionSequences $instructionSequences;

    protected array $globalObjectTable = [];

    public function __construct(
        public readonly RubyVMInterface $vm,
        public readonly Verifier $verifier,
    ) {
        $this->instructionSequenceList = new Offsets();
        $this->globalObjectList = new Offsets();
        $this->instructionSequences = new InstructionSequences();
    }

    public function process(): ExecutorInterface
    {
        if (!isset($this->vm)) {
            throw new RubyVMException(
                'The RubyVM is not set'
            );
        }
        $aux = new Aux(
            loader: new AuxLoader(
                obj: 0,
                index: 0,
            ),
        );

        /**
         * @var InstructionSequence|null $instructionSequence
         */
        $instructionSequence = $this->instructionSequences->get($aux->loader->index);

        if (!$instructionSequence) {
            $instructionSequence = new InstructionSequence(
                aux: $aux,
                processor: new InstructionSequenceProcessor(
                    kernel: $this,
                    aux: $aux,
                ),
            );

            $instructionSequence->load();

            $this->instructionSequences->set(
                $aux->loader->index,
                $instructionSequence,
            );
        }

        $operationProcessorEntries = new DefaultOperationProcessorEntries();

        return new Executor(
            new Main(
                $this->vm->option()->stdOut ?? new StreamHandler(STDOUT),
                $this->vm->option()->stdIn ?? new StreamHandler(STDIN),
                $this->vm->option()->stdErr ?? new StreamHandler(STDERR),
            ),
            $operationProcessorEntries,
            $instructionSequence,
            $this->vm->option()->logger,
        );
    }

    /**
     * Setup RubyVM
     *
     * @see https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L11087
     */
    public function setup(): self
    {
        $this->vm->option()->logger->info(
            sprintf('Load an instruction sequence header'),
        );
        $pos = $this->stream()->pos();

        $this->magic = $this->stream()->read(4);
        $this->majorVersion = $this->stream()->unsignedLong();
        $this->minorVersion = $this->stream()->unsignedLong();
        $this->size = $this->stream()->unsignedLong();
        $this->extraSize = $this->stream()->unsignedLong();
        $this->instructionSequenceListSize = $this->stream()->unsignedLong();
        $this->globalObjectListSize = $this->stream()->unsignedLong();
        $this->instructionSequenceListOffset = $this->stream()->unsignedLong();
        $this->globalObjectListOffset = $this->stream()->unsignedLong();

        $this->vm->option()->logger->info(
            sprintf('Loaded an instruction sequence header (%d bytes)', $this->stream()->pos() - $pos),
        );

        $this->setupInstructionSequenceList();
        $this->setupGlobalObjectList();

        $this->verifier->verify(
            new VerificationHeader($this),
        );

        return $this;
    }

    /**
     * Setup instruction sequence offsets
     *
     * @return $this
     */
    private function setupInstructionSequenceList(): self
    {
        $this->stream()->pos($this->instructionSequenceListOffset);

        $this->vm->option()->logger->info(
            sprintf('Setup an instruction sequence list (offset: %d)', $this->instructionSequenceListOffset),
        );

        for ($i = 0; $i < $this->instructionSequenceListSize; $i++) {
            $this->instructionSequenceList->append(
                new Offset(
                    $this->stream()->unsignedByte(),
                )
            );
        }

        $this->vm->option()->logger->info(
            sprintf('Loaded an instruction sequence list (size: %d)', $this->stream()->pos() - $this->instructionSequenceListOffset),
        );

        return $this;
    }

    /**
     * Setup global object offsets
     *
     * @return $this
     */
    private function setupGlobalObjectList(): self
    {
        $this->stream()->pos($this->globalObjectListOffset);

        $this->vm->option()->logger->info(
            sprintf('Setup a global object list (offset: %d)', $this->globalObjectListOffset),
        );

        for ($i = 0; $i < $this->globalObjectListSize; $i++) {
            $this->globalObjectList->append(
                new Offset(
                    $this->stream()->unsignedLong(),
                )
            );
        }

        $this->vm->option()->logger->info(
            sprintf('Loaded global object list (size: %d)', $this->stream()->pos() - $this->globalObjectListOffset),
        );

        return $this;
    }

    public function expectedVersions(): array
    {
        return [RubyVersion::VERSION_3_2];
    }

    public function stream(): BinaryStreamReaderInterface
    {
        return $this->vm->option()->reader;
    }

    public function findId(int $index): ID
    {
        return $this->findObject($index)->id;
    }

    public function findObject(int $index): Object_
    {
        if (!isset($this->globalObjectList[$index])) {
            throw new RubyVMException(
                sprintf(
                    'Cannot resolve to refer index#%d in the global object list',
                    $index,
                )
            );
        }

        $this->vm->option()->logger->info(
            sprintf('Start to find object (index: %d)', $index),
        );

        if (isset($this->globalObjectTable[$index])) {
            /**
             * @var Object_ $object
             */
            $object = $this->globalObjectTable[$index];

            $this->vm->option()->logger->debug(
                sprintf('Use cached object (index: %d)', $index),
            );

            $this->vm->option()->logger->info(
                sprintf(
                    'Loaded an object (index: %d, type: %s, special_const: %d, frozen: %d, internal: %d)',
                    $index,
                    $object->info->type->name,
                    $object->info->specialConst,
                    $object->info->frozen,
                    $object->info->internal,
                ),
            );

            return $object;
        }

        $this->vm->option()->logger->debug(
            sprintf('Start to register new object (index: %d)', $index),
        );

        /**
         * @var Offset $offset
         */
        $offset = $this->globalObjectList[$index];

        /**
         * @var ObjectInfo $info
         */
        $info = $this
            ->stream()
            ->dryPosTransaction(
                function (BinaryStreamReader $stream) use ($offset) {
                    $stream->pos($offset->offset);
                    $byte = $stream->unsignedByte();
                    return new ObjectInfo(
                        type:         SymbolType::of(($byte >> 0) & 0x1f),
                        specialConst: (bool) ($byte >> 5) & 0x01,
                        frozen:       (bool) ($byte >> 6) & 0x01,
                        internal:     (bool) ($byte >> 7) & 0x01,
                    );
                }
            );

        $this->vm->option()->logger->info(
            sprintf(
                'Loaded an object (index: %d, type: %s, special_const: %d, frozen: %d, internal: %d)',
                $index,
                $info->type->name,
                $info->specialConst,
                $info->frozen,
                $info->internal,
            ),
        );

        return $this->globalObjectTable[$index] = new Object_(
            info: $info,
            symbol: $this
                ->stream()
                ->dryPosTransaction(
                    fn () => $this->resolveLoader($info, $offset->increase())
                        ->load()
                ),
            offset: $offset,
        );
    }

    private function resolveLoader(ObjectInfo $info, Offset $offset): LoaderInterface
    {
        return match ($info->type) {
            SymbolType::TRUE => new TrueLoader($this, $offset),
            SymbolType::FALSE => new FalseLoader($this, $offset),
            SymbolType::FLOAT => new FloatLoader($this, $offset),
            SymbolType::FIXNUM => new FixedNumberLoader($this, $offset),
            SymbolType::SYMBOL => new SymbolLoader($this, $offset),
            SymbolType::STRING => new StringLoader($this, $offset),
            default => throw new ResolverException("Cannot resolve a symbol: {$info->type->name} - maybe the symbol type is not supported yet"),
        };
    }
}
