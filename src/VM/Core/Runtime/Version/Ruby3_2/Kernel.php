<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2;

use RubyVM\VM\Core\Helper\DefaultOperationProcessorEntries;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorEntries;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequences;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Offset\Offsets;
use RubyVM\VM\Core\Runtime\RubyVersion;
use RubyVM\VM\Core\Runtime\RubyVM;
use RubyVM\VM\Core\Runtime\RubyVMInterface;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\LoaderInterface;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\ObjectInfo;
use RubyVM\VM\Core\Runtime\Symbol\SymbolType;
use RubyVM\VM\Core\Runtime\Verification\Verifier;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence\InstructionSequenceProcessor;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\StringLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\SymbolLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard\Main;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Verification\VerificationHeader;
use RubyVM\VM\Exception\ResolverException;
use RubyVM\VM\Exception\RubyVMException;
use RubyVM\VM\Stream\BinaryStreamReader;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;

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
    )
    {
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
            new Main(),
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
        $this->magic = $this->stream()->read(4);
        $this->majorVersion = $this->stream()->unsignedLong();
        $this->minorVersion = $this->stream()->unsignedLong();
        $this->size = $this->stream()->unsignedLong();
        $this->extraSize = $this->stream()->unsignedLong();
        $this->instructionSequenceListSize = $this->stream()->unsignedLong();
        $this->globalObjectListSize = $this->stream()->unsignedLong();
        $this->instructionSequenceListOffset = $this->stream()->unsignedLong();
        $this->globalObjectListOffset = $this->stream()->unsignedLong();

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

        for ($i = 0; $i < $this->instructionSequenceListSize; $i++) {
            $this->instructionSequenceList->append(new Offset(
                $this->stream()->unsignedByte(),
            ));
        }

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

        for ($i = 0; $i < $this->globalObjectListSize; $i++) {
            $this->globalObjectList->append(new Offset(
                $this->stream()->unsignedLong(),
            ));
        }
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

        if (isset($this->globalObjectTable[$index])) {
            return $this->globalObjectTable[$index];
        }

        /**
         * @var Offset $offset
         */
        $offset = $this->globalObjectList[$index];

        /**
         * @var ObjectInfo $info
         */
        $info = $this
            ->stream()
            ->dryPosTransaction(function (BinaryStreamReader $stream) use ($offset) {
                $stream->pos($offset->offset);
                $byte = $stream->unsignedByte();
                return new ObjectInfo(
                    type:         SymbolType::of(($byte >> 0) & 0x1f),
                    specialCount: (bool) ($byte >> 5) & 0x01,
                    frozen:       (bool) ($byte >> 6) & 0x01,
                    internal:     (bool) ($byte >> 7) & 0x01,
                );
            });

        return $this->globalObjectTable[$index] = new Object_(
            offset: $offset,
            info: $info,
            symbol: $this
                ->stream()
                ->dryPosTransaction(
                    fn () => $this->resolveLoader($info, $offset->increase())
                        ->load()
                ),
        );
    }

    private function resolveLoader(ObjectInfo $info, Offset $offset): LoaderInterface
    {
        return match ($info->type) {
            SymbolType::SYMBOL => new SymbolLoader($this, $offset),
            SymbolType::STRING => new StringLoader($this, $offset),
            default => throw new ResolverException("Cannot resolve a symbol: {$info->type->name} - maybe the symbol type is not supported yet"),
        };
    }
}
