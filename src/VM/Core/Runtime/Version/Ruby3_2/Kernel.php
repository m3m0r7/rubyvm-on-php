<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2;

use RubyVM\VM\Core\Helper\DefaultDefinedClassEntries;
use RubyVM\VM\Core\Helper\DefaultOperationProcessorEntries;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Executor\IOContext;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorEntries;
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
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;
use RubyVM\VM\Core\Runtime\Verification\Verifier;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence\InstructionSequenceProcessor;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\ArrayLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\FalseLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\FixedNumberLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\FloatLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\NilLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\StringLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\StructLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\SymbolLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader\TrueLoader;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard\Main;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Verification\VerificationHeader;
use RubyVM\VM\Exception\ResolverException;
use RubyVM\VM\Exception\RubyVMException;
use RubyVM\VM\Stream\RubyVMBinaryStreamReader;
use RubyVM\VM\Stream\RubyVMBinaryStreamReaderInterface;
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
    protected readonly IOContext $IOContext;

    protected array $globalObjectTable = [];

    public readonly string $rubyPlatform;

    public readonly string $extraData;

    private RubyVMBinaryStreamReaderInterface $stream;
    private UserlandHeapSpace $userlandHeapSpace;

    public function __construct(
        public readonly RubyVMInterface $vm,
        public readonly Verifier $verifier,
    ) {
        $this->instructionSequenceList = new Offsets();
        $this->globalObjectList = new Offsets();
        $this->instructionSequences = new InstructionSequences();
        $this->IOContext = new IOContext(
            $this->vm->option()->stdOut ?? new StreamHandler(STDOUT),
            $this->vm->option()->stdIn ?? new StreamHandler(STDIN),
            $this->vm->option()->stdErr ?? new StreamHandler(STDERR),
        );
        $this->userlandHeapSpace = new UserlandHeapSpace();
    }

    public function process(): ExecutorInterface
    {
        if (!isset($this->vm)) {
            throw new RubyVMException('The RubyVM is not set');
        }
        $aux = new Aux(
            loader: new AuxLoader(
                index: 0,
            ),
        );

        /**
         * @var null|InstructionSequence $instructionSequence
         */
        $instructionSequence = $this->loadInstructionSequence($aux);

        $main = new Main();
        $main->injectVMContext(
            $this,
            new DefaultDefinedClassEntries(),
        );
        $executor = new Executor(
            $this,
            $main,
            $instructionSequence,
            $this->vm->option()->logger,
            $this->userLandHeapSpace(),
        );

        $executor->context()->appendTrace('<main>');

        return $executor;
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
            sprintf('Load an instruction sequence header'),
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
        $this->stream()->pos($this->instructionSequenceListOffset);

        $this->vm->option()->logger->info(
            sprintf('Setup an instruction sequence list (offset: %d)', $this->instructionSequenceListOffset),
        );

        for ($i = 0; $i < $this->instructionSequenceListSize; ++$i) {
            $this->instructionSequenceList
                ->append(
                    new Offset(
                        // VALUE iseq_list;       /* [iseq0, ...] */
                        $this->stream()->readAsUnsignedLong(),
                    )
                );
        }

        $this->vm->option()->logger->info(
            sprintf('Loaded an instruction sequence list (size: %d)', $this->stream()->pos() - $this->instructionSequenceListOffset),
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
        $this->stream()->pos($this->globalObjectListOffset);

        $this->vm->option()->logger->info(
            sprintf('Setup a global object list (offset: %d)', $this->globalObjectListOffset),
        );

        for ($i = 0; $i < $this->globalObjectListSize; ++$i) {
            $this->globalObjectList->append(
                new Offset(
                    $this->stream()->readAsUnsignedLong(),
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

    public function stream(): RubyVMBinaryStreamReaderInterface
    {
        return $this->stream ??= new RubyVMBinaryStreamReader($this->vm->option()->reader);
    }

    public function findId(int $index): ID
    {
        $this->vm->option()->logger->info(
            sprintf('Start to find object by ID (index: %d)', $index),
        );

        return $this->findObject($index)->id;
    }

    public function findObject(int $index): Object_
    {
        if (!isset($this->globalObjectList[$index])) {
            throw new RubyVMException(sprintf('Cannot resolve to refer index#%d in the global object list', $index));
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

        if (method_exists($symbol, 'injectVMContext')) {
            $symbol->injectVMContext($this);
        }

        return $this->globalObjectTable[$index] = $symbol->toObject($offset);
    }

    private function resolveLoader(ObjectInfo $info, Offset $offset): LoaderInterface
    {
        return match ($info->type) {
            SymbolType::NIL => new NilLoader($this, $offset),
            SymbolType::STRUCT => new StructLoader($this, $offset),
            SymbolType::TRUE => new TrueLoader($this, $offset),
            SymbolType::FALSE => new FalseLoader($this, $offset),
            SymbolType::FLOAT => new FloatLoader($this, $offset),
            SymbolType::FIXNUM => new FixedNumberLoader($this, $offset),
            SymbolType::SYMBOL => new SymbolLoader($this, $offset),
            SymbolType::STRING => new StringLoader($this, $offset),
            SymbolType::ARRAY => new ArrayLoader($this, $offset),
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

        if (!$instructionSequence) {
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

        return $instructionSequence;
    }

    public function operationProcessorEntries(): OperationProcessorEntries
    {
        static $operationProcessorEntries = new DefaultOperationProcessorEntries();

        return $operationProcessorEntries;
    }

    public function IOContext(): IOContext
    {
        return $this->IOContext;
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

    public function userlandHeapSpace(): UserlandHeapSpace
    {
        return $this->userlandHeapSpace;
    }
}
