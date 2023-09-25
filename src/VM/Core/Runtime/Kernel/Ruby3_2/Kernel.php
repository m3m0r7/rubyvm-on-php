<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2;

use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyVMInterface;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\HeapSpace\DefaultInstanceHeapSpace;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\InstructionSequence\InstructionSequenceProcessor;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader\ArraySymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader\BooleanSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader\FixedNumberSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader\FloatSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader\NilSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader\StringSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader\StructSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader\SymbolSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Verification\VerificationHeader;
use RubyVM\VM\Core\Runtime\Verification\Verifier;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceEntries;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offsets;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;
use RubyVM\VM\Core\YARV\Essential\ID;
use RubyVM\VM\Core\YARV\Essential\Symbol\ObjectInfo;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolLoaderInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolType;
use RubyVM\VM\Core\YARV\RubyVersion;
use RubyVM\VM\Exception\ResolverException;
use RubyVM\VM\Exception\RubyVMException;
use RubyVM\VM\Stream\RubyVMBinaryStreamReader;
use RubyVM\VM\Stream\RubyVMBinaryStreamReaderInterface;

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

    protected readonly InstructionSequenceEntries $instructionSequences;

    /**
     * @var SymbolInterface[]
     */
    protected array $globalObjectTable = [];

    public readonly string $rubyPlatform;

    public readonly string $extraData;

    private RubyVMBinaryStreamReaderInterface $stream;

    public function __construct(
        public readonly RubyVMInterface $vm,
        public readonly Verifier $verifier,
    ) {
        $this->instructionSequenceList = new Offsets();
        $this->globalObjectList = new Offsets();
        $this->instructionSequences = new InstructionSequenceEntries();
    }

    public function __debugInfo(): array
    {
        return [];
    }

    /**
     * Setup RubyVM.
     *
     * @see https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L11087
     */
    public function setup(): self
    {
        $this->vm->option()->logger->info(
            'Load an instruction sequence header',
        );
        $pos = $this->stream()->pos();

        $this->magic = $this->stream()->read(4);
        $this->majorVersion = $this->stream()->readAsUnsignedLong();
        $this->minorVersion = $this->stream()->readAsUnsignedLong();
        $this->size = $this->stream()->readAsUnsignedLong();
        $this->extraSize = $this->stream()->readAsUnsignedLong();
        $this->instructionSequenceListSize = $this->stream()->readAsUnsignedLong();
        $this->globalObjectListSize = $this->stream()->readAsUnsignedLong();
        $this->instructionSequenceListOffset = $this->stream()->readAsUnsignedLong();
        $this->globalObjectListOffset = $this->stream()->readAsUnsignedLong();
        $this->rubyPlatform = $this->stream()->readAsString();

        $this->vm->option()->logger->info(
            sprintf('Loaded an instruction sequence header (%d bytes)', $this->stream()->pos() - $pos),
        );

        $this->setupInstructionSequenceList();
        $this->setupGlobalObjectList();
        $this->setupExtraData();

        $this->verifier->verify(
            new VerificationHeader($this),
        );

        return $this;
    }

    /**
     * Setup instruction sequence offsets.
     *
     * @return $this
     */
    private function setupInstructionSequenceList(): self
    {
        $reader = $this->stream()->duplication();
        $reader->pos($this->instructionSequenceListOffset);

        $this->vm->option()->logger->info(
            sprintf('Setup an instruction sequence list (offset: %d)', $this->instructionSequenceListOffset),
        );

        for ($i = 0; $i < $this->instructionSequenceListSize; ++$i) {
            $this->instructionSequenceList
                ->append(
                    new Offset(
                        // VALUE iseq_list;       /* [iseq0, ...] */
                        $reader->readAsUnsignedLong(),
                    )
                );
        }

        $this->vm->option()->logger->info(
            sprintf('Loaded an instruction sequence list (size: %d)', $reader->pos() - $this->instructionSequenceListOffset),
        );

        return $this;
    }

    /**
     * Setup global object offsets.
     *
     * @return $this
     */
    private function setupGlobalObjectList(): self
    {
        $reader = $this->stream()->duplication();
        $reader->pos($this->globalObjectListOffset);

        $this->vm->option()->logger->info(
            sprintf('Setup a global object list (offset: %d)', $this->globalObjectListOffset),
        );

        for ($i = 0; $i < $this->globalObjectListSize; ++$i) {
            $this->globalObjectList->append(
                new Offset(
                    $reader->readAsUnsignedLong(),
                )
            );
        }

        $this->vm->option()->logger->info(
            sprintf('Loaded global object list (size: %d)', $reader->pos() - $this->globalObjectListOffset),
        );

        return $this;
    }

    public function expectedVersions(): array
    {
        return [RubyVersion::VERSION_3_2];
    }

    public function stream(): RubyVMBinaryStreamReaderInterface
    {
        return $this->stream ??= new RubyVMBinaryStreamReader($this->vm->option()->reader);
    }

    public function findId(int $index): ID
    {
        $this->vm->option()->logger->info(
            sprintf('Start to find object by ID (index: %d)', $index),
        );

        return new ID($this->findObject($index));
    }

    public function findObject(int $index): SymbolInterface
    {
        if (!isset($this->globalObjectList[$index])) {
            throw new RubyVMException(sprintf('Cannot resolve to refer index#%d in the global object list', $index));
        }

        $this->vm->option()->logger->info(
            sprintf('Start to find object (index: %d)', $index),
        );

        if (isset($this->globalObjectTable[$index])) {
            $symbol = $this->globalObjectTable[$index];

            $this->vm->option()->logger->debug(
                sprintf('Use cached object (index: %d)', $index),
            );

            return $symbol;
        }

        $this->vm->option()->logger->debug(
            sprintf('Start to register new object (index: %d)', $index),
        );

        /**
         * @var Offset $offset
         */
        $offset = $this->globalObjectList[$index];

        $reader = $this->stream()->duplication();
        $reader->pos($offset->offset);

        $byte = $reader->readAsUnsignedByte();
        $info = new ObjectInfo(
            type: SymbolType::of(($byte >> 0) & 0x1F),
            specialConst: (bool) ($byte >> 5) & 0x01,
            frozen: (bool) ($byte >> 6) & 0x01,
            internal: (bool) ($byte >> 7) & 0x01,
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

        $symbol = $this->resolveLoader($info, $offset->increase())
            ->load();

        return $this->globalObjectTable[$index] = $symbol;
    }

    private function resolveLoader(ObjectInfo $info, Offset $offset): SymbolLoaderInterface
    {
        return match ($info->type) {
            SymbolType::NIL => new NilSymbolLoader($this, $offset),
            SymbolType::STRUCT => new StructSymbolLoader($this, $offset),
            SymbolType::FALSE,
            SymbolType::TRUE => new BooleanSymbolLoader($this, $offset),
            SymbolType::FLOAT => new FloatSymbolLoader($this, $offset),
            SymbolType::FIXNUM => new FixedNumberSymbolLoader($this, $offset),
            SymbolType::SYMBOL => new SymbolSymbolLoader($this, $offset),
            SymbolType::STRING => new StringSymbolLoader($this, $offset),
            SymbolType::ARRAY => new ArraySymbolLoader($this, $offset),
            default => throw new ResolverException("Cannot resolve a symbol: {$info->type->name} - maybe the symbol type is not supported yet"),
        };
    }

    private function setupExtraData(): void
    {
        $reader = $this->stream()->duplication();
        $reader->pos($this->size);

        $this->extraData = $reader->read($this->extraSize);
    }

    public function loadInstructionSequence(Aux $aux): InstructionSequence
    {
        $instructionSequence = $this
            ->instructionSequences
            ->get($aux->loader->index);

        if ($instructionSequence) {
            return $instructionSequence;
        }

        // load all sequences
        for ($i = 0; $i < $this->instructionSequenceListSize; ++$i) {
            $targetAux = new Aux(
                loader: new AuxLoader(
                    $i,
                ),
            );
            $instructionSequence = new InstructionSequence(
                aux: $targetAux,
                processor: new InstructionSequenceProcessor(
                    kernel: $this,
                    aux: $targetAux,
                ),
            );

            $instructionSequence->load();

            $this->instructionSequences->set(
                $targetAux->loader->index,
                $instructionSequence,
            );
        }

        return $this->loadInstructionSequence($aux);
    }

    public function rubyPlatform(): string
    {
        return $this->rubyPlatform;
    }

    public function minorVersion(): int
    {
        return $this->minorVersion;
    }

    public function majorVersion(): int
    {
        return $this->majorVersion;
    }

    public function extraData(): string
    {
        return $this->extraData;
    }

    public function userlandHeapSpace(): UserlandHeapSpaceInterface
    {
        static $userlandHeapSpace;

        return $userlandHeapSpace ??= new DefaultInstanceHeapSpace();
    }

    public function instructionSequenceListSize(): int
    {
        return $this->instructionSequenceListSize;
    }

    public function instructionSequenceListOffset(): int
    {
        return $this->instructionSequenceListOffset;
    }

    public function instructionSequenceList(): Offsets
    {
        return $this->instructionSequenceList;
    }
}
