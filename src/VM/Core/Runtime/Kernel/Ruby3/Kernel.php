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

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3;

use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyVMInterface;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\HeapSpace\DefaultInstanceHeapSpace;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\ArraySymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\BooleanSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\CaseDispatchSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\FixedNumberSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\FloatSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\NilSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\RegexpSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\StringSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\StructSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Loader\SymbolSymbolLoader;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3\Verification\VerificationHeader;
use RubyVM\VM\Core\Runtime\Verification\Verifier;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceEntries;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceProcessorInterface;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offsets;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;
use RubyVM\VM\Core\YARV\Essential\ID;
use RubyVM\VM\Core\YARV\Essential\Symbol\ObjectInfo;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolLoaderInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolType;
use RubyVM\VM\Exception\ResolverException;
use RubyVM\VM\Exception\RubyVMException;
use RubyVM\VM\Stream\RubyVMBinaryStreamReader;
use RubyVM\VM\Stream\RubyVMBinaryStreamReaderInterface;

abstract class Kernel implements KernelInterface
{
    protected string $magic;

    protected int $majorVersion;

    protected int $minorVersion;

    protected int $size;

    protected int $extraSize;

    protected int $instructionSequenceListSize;

    protected int $globalObjectListSize;

    protected int $instructionSequenceListOffset;

    protected int $globalObjectListOffset;

    protected Offsets $instructionSequenceList;

    protected Offsets $globalObjectList;

    protected readonly InstructionSequenceEntries $instructionSequences;

    /**
     * @var SymbolInterface[]
     */
    protected array $globalObjectTable = [];

    protected string $extraData;

    private RubyVMBinaryStreamReaderInterface $stream;

    public function __construct(
        protected RubyVMInterface $vm,
        protected Verifier $verifier,
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
    public function setup(): KernelInterface
    {
        $this->vm->option()->logger->info(
            'Load an instruction sequence header',
        );
        $pos = $this->stream()->pos();

        $this->setupHeaders();

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

    protected function setupHeaders(): KernelInterface
    {
        $this->magic = $this->stream()->read(4);
        $this->majorVersion = $this->stream()->readAsUnsignedLong();
        $this->minorVersion = $this->stream()->readAsUnsignedLong();

        return $this;
    }

    /**
     * Setup instruction sequence offsets.
     *
     * @return $this
     */
    protected function setupInstructionSequenceList(): KernelInterface
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
    protected function setupGlobalObjectList(): KernelInterface
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
            SymbolType::REGEXP => new RegexpSymbolLoader($this, $offset),
            SymbolType::HASH => new CaseDispatchSymbolLoader($this, $offset),
            default => throw new ResolverException("Cannot resolve a symbol: {$info->type->name} - maybe the symbol type is not supported yet"),
        };
    }

    protected function setupExtraData(): void
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
                processor: $this->createInstructionSequenceProcessor($targetAux),
            );

            $instructionSequence->load();

            $this->instructionSequences->set(
                $targetAux->loader->index,
                $instructionSequence,
            );
        }

        return $this->loadInstructionSequence($aux);
    }

    abstract protected function createInstructionSequenceProcessor(Aux $aux): InstructionSequenceProcessorInterface;

    public function rubyPlatform(): ?string
    {
        return null;
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

    public function size(): int
    {
        return $this->size;
    }

    public function extraSize(): int
    {
        return $this->extraSize;
    }

    public function magic(): string
    {
        return $this->magic;
    }

    public function globalObjectListSize(): int
    {
        return $this->globalObjectListSize;
    }

    public function globalObjectListOffset(): int
    {
        return $this->globalObjectListOffset;
    }
}
