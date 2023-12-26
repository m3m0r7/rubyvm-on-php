<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_3\InstructionSequence;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Offset;
use RubyVM\VM\Core\Runtime\ClassCreator;
use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\InsnType;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operation;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperationEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\CallData;
use RubyVM\VM\Core\YARV\Criterion\Entry\CallInfo;
use RubyVM\VM\Core\YARV\Criterion\Entry\CallInfoEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\Catch_;
use RubyVM\VM\Core\YARV\Criterion\Entry\CatchEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\InsnsBodyEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\InsnsPositionEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\OuterVariable;
use RubyVM\VM\Core\YARV\Criterion\Entry\OuterVariableEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\Variable as VariableEntry;
use RubyVM\VM\Core\YARV\Criterion\Entry\VariableEntries;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceBody;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceBodyInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InstructionSequenceProcessorInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Keyword;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset as OffsetCriterion;
use RubyVM\VM\Core\YARV\Essential\ID;
use RubyVM\VM\Core\YARV\Essential\Symbol\OffsetSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\InstructionSequenceProcessorException;

class InstructionSequenceProcessor implements InstructionSequenceProcessorInterface
{
    protected SymbolInterface $path;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly Aux $aux,
    ) {
        if (isset($this->kernel->instructionSequenceList[$this->aux->loader->index])) {
            return;
        }

        throw new InstructionSequenceProcessorException(sprintf('Not found instructionSequenceList#%d', $this->aux->loader->index));
    }

    public function __debugInfo(): array
    {
        return [];
    }

    public function process(): InstructionSequenceBodyInterface
    {
        /**
         * @var OffsetCriterion $offset
         */
        $offset = $this->kernel->instructionSequenceList()[$this->aux->loader->index];
        $this->kernel->stream()->pos($offset->offset);

        $computeFromBodyOffset = static fn (int $x) => $offset->offset - $x;

        $type = $this->kernel->stream()->smallValue();
        $iseqSize = $this->kernel->stream()->smallValue();

        $bytecodeOffset = $computeFromBodyOffset($this->kernel->stream()->smallValue());
        $bytecodeSize = $this->kernel->stream()->smallValue();

        $paramFlags = $this->kernel->stream()->smallValue();
        $paramSize = $this->kernel->stream()->smallValue();
        $paramLeadNum = $this->kernel->stream()->smallValue();
        $paramOptNum = $this->kernel->stream()->smallValue();
        $paramRestStart = $this->kernel->stream()->smallValue();
        $paramPostStart = $this->kernel->stream()->smallValue();
        $paramPostNum = $this->kernel->stream()->smallValue();
        $paramBlockStart = $this->kernel->stream()->smallValue();
        $paramOptTableOffset = $computeFromBodyOffset($this->kernel->stream()->smallValue());
        $paramKeywordOffset = $this->kernel->stream()->smallValue();
        $locationPathObjIndex = $this->kernel->stream()->smallValue();
        $locationBaseLabelIndex = $this->kernel->stream()->smallValue();
        $locationLabelIndex = $this->kernel->stream()->smallValue();
        $locationFirstLineNo = $this->kernel->stream()->smallValue();
        $locationNodeId = $this->kernel->stream()->smallValue();
        $locationCodeLocationBegPosLineNo = $this->kernel->stream()->smallValue();
        $locationCodeLocationBegPosColumn = $this->kernel->stream()->smallValue();
        $locationCodeLocationEndPosLineNo = $this->kernel->stream()->smallValue();
        $locationCodeLocationEndPosColumn = $this->kernel->stream()->smallValue();
        $insnsInfoBodyOffset = $computeFromBodyOffset($this->kernel->stream()->smallValue());
        $insnsInfoPositionsOffset = $computeFromBodyOffset($this->kernel->stream()->smallValue());
        $insnsInfoSize = $this->kernel->stream()->smallValue();
        $localTableOffset = $computeFromBodyOffset($this->kernel->stream()->smallValue());
        $catchTableSize = $this->kernel->stream()->smallValue();
        $catchTableOffset = $computeFromBodyOffset($this->kernel->stream()->smallValue());
        $parentISeqIndex = $this->kernel->stream()->smallValue();
        $localISeqIndex = $this->kernel->stream()->smallValue();
        $mandatoryOnlyIseqIndex = $this->kernel->stream()->smallValue();
        $ciEntriesOffset = $computeFromBodyOffset($v = $this->kernel->stream()->smallValue());
        $outerVariablesOffset = $computeFromBodyOffset($this->kernel->stream()->smallValue());
        $variableFlipCount = $this->kernel->stream()->smallValue();
        $localTableSize = $this->kernel->stream()->smallValue();

        $ivcSize = $this->kernel->stream()->smallValue();
        $icvArcSize = $this->kernel->stream()->smallValue();
        $iseSize = $this->kernel->stream()->smallValue();
        $icSize = $this->kernel->stream()->smallValue();
        $ciSize = $this->kernel->stream()->smallValue();
        $stackMax = $this->kernel->stream()->smallValue();

        $catchExceptP = $this->kernel->stream()->smallValue();

        $builtinInlineP = $this->kernel->stream()->smallValue();

        $this->path = $this->kernel->findObject($locationPathObjIndex);

        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L12298
        $callInfoEntries = $this->loadCallInfoEntries($ciEntriesOffset, $ciSize);
        $outerVariables = $this->loadOuterVariables($outerVariablesOffset);
        $paramOptTable = $this->loadParamOptTable($paramOptTableOffset, $paramOptNum);

        $keyword = $this->loadKeyword($paramKeywordOffset);
        $insnsBody = $this->loadInsnsBody($insnsInfoBodyOffset, $insnsInfoSize);
        $insnsPositions = $this->loadInsnsPositions($insnsInfoBodyOffset, $insnsInfoSize);
        $localTable = $this->loadLocalTable($localTableOffset, $localTableSize);
        $catchTable = $this->loadCatchTable($catchTableOffset, $catchTableSize);

        $parentISeq = $parentISeqIndex > 0
            ? new InstructionSequence(
                aux: $aux = new Aux(
                    loader: new AuxLoader(
                        index: $parentISeqIndex,
                    ),
                ),
                processor: new self(
                    kernel: $this->kernel,
                    aux: $aux,
                ),
            )
            : null;

        $localISeq = $localISeqIndex > 0
            ? new InstructionSequence(
                aux: $aux = new Aux(
                    loader: new AuxLoader(
                        index: $localISeqIndex,
                    ),
                ),
                processor: new self(
                    kernel: $this->kernel,
                    aux: $aux,
                ),
            )
            : null;
        $mandatoryOnlyISeq = $mandatoryOnlyIseqIndex > 0
            ? new InstructionSequence(
                aux: $aux = new Aux(
                    loader: new AuxLoader(
                        index: $mandatoryOnlyIseqIndex,
                    ),
                ),
                processor: new self(
                    kernel: $this->kernel,
                    aux: $aux,
                ),
            )
            : null;

        $rbInstructionSequenceBody = new InstructionSequenceInfo(
            path: $this->path(),
            type: $type,
            stackMax: $stackMax,
            iseqSize: $iseqSize,
            objectParam: new ObjectParameter(
                objectParamFlags: new ObjectParameterFlags(
                    hasLead: (bool) (($paramFlags >> 0) & 1),
                    hasOpt: (bool) (($paramFlags >> 1) & 1),
                    hasRest: (bool) (($paramFlags >> 2) & 1),
                    hasPost: (bool) (($paramFlags >> 3) & 1),
                    keyword: $keyword,
                    hasKeyword: (bool) (($paramFlags >> 4) & 1),
                    hasKeywordRest: (bool) (($paramFlags >> 5) & 1),
                    hasBlock: (bool) (($paramFlags >> 6) & 1),
                    ambiguousParam: (bool) (($paramFlags >> 7) & 1),
                    acceptsNoKeywordArg: (bool) (($paramFlags >> 8) & 1),
                    ruby2Keywords: (bool) (($paramFlags >> 9) & 1),
                ),
                size: $paramSize,
                leadNum: $paramLeadNum,
                optNum: $paramOptNum,
                restStart: $paramRestStart,
                postStart: $paramPostStart,
                postNum: $paramPostNum,
                blockStart: $paramBlockStart,
                optTable: null, // TODO: Implement here
            ),
            localTableSize: $localTableSize,
            ciSize: $ciSize,
            insns: new Insns(
                size: $insnsInfoSize,
                body: null, // TODO: Implement here
                positions: null, // TODO: Implement here
            ),
            variable: new Variable(
                flipCount: $variableFlipCount,
                scriptLines: null, // TODO: Implement here
            ),
            location: new Location(
                firstLineNo: $locationFirstLineNo,
                nodeId: $locationNodeId,
                codeLocation: new CodeLocation(
                    begin: new CodePosition(
                        lineNumber: $locationCodeLocationBegPosLineNo,
                        column: $locationCodeLocationBegPosColumn
                    ),
                    end: new CodePosition(
                        lineNumber: $locationCodeLocationEndPosLineNo,
                        column: $locationCodeLocationEndPosColumn
                    ),
                )
            ),
            catchExceptP: $catchExceptP,
            builtinInlineP: $builtinInlineP,
            ivcSize: $ivcSize,
            icvArcSize: $icvArcSize,
            iseSize: $iseSize,
            icSize: $icSize,
            outerVariables: $outerVariables,
            variableEntries: $localTable,
            catchTable: $catchTable,
            parentISeq: $parentISeq,
            localISeq: $localISeq,
            mandatoryOnlyISeq: $mandatoryOnlyISeq,
            callInfoEntries: $callInfoEntries,
            bytecodeOffset: $bytecodeOffset,
            bytecodeSize: $bytecodeSize,
        );

        $rbInstructionSequenceBody
            ->setOperationEntries(
                $this->loadCode(
                    bytecodeOffset: $bytecodeOffset,
                    bytecodeSize: $bytecodeSize,
                    instructionSequenceSize: $iseqSize,
                    instructionSequenceBody: $rbInstructionSequenceBody,
                ),
            );

        return new InstructionSequenceBody(
            $rbInstructionSequenceBody,
        );
    }

    private function loadCode(
        int $bytecodeOffset,
        int $bytecodeSize,
        int $instructionSequenceSize,
        InstructionSequenceInfo $instructionSequenceBody,
    ): OperationEntries {
        $reader = $this->kernel->stream()->duplication();

        $entries = new OperationEntries();
        $operationMap = implode('', $this->insnOperationsOperands());
        $callInfoEntryIndex = 0;
        $reader->pos($bytecodeOffset);

        for ($codeIndex = 0; $codeIndex < $instructionSequenceSize;) {
            $insn = Insn::of($insnValue = $reader->smallValue());
            $entries->append(
                new Operation(
                    insn: $insn,
                )
            );

            // NOTE: Here is getting operand types for currently opcode
            $offset = $this->insnOperationOperandsOffsets()[$insnValue];
            $types = '';

            // Read to null byte
            for ($i = $offset; $i < strlen($operationMap) && $operationMap[$i] !== "\0"; ++$i) {
                $types .= $operationMap[$i];
            }

            ++$codeIndex;

            for ($opIndex = 0; ord($types[$opIndex] ?? "\0"); $opIndex++, $codeIndex++) {
                $operandType = InsnType::of($types[$opIndex]);
                $entries->append(
                    match ($operandType) {
                        InsnType::TS_VALUE => new Operand(
                            operand: ClassCreator::createClassBySymbol(
                                $this->kernel
                                    ->findObject($reader->smallValue())
                            ),
                        ),
                        InsnType::TS_CALLDATA => new Operand(
                            operand: $instructionSequenceBody
                                ->callInfoEntries[$callInfoEntryIndex++],
                        ),

                        // see: https://github.com/ruby/ruby/blob/ruby_3_2/iseq.c#L2090
                        InsnType::TS_NUM,
                        InsnType::TS_LINDEX => new Operand(
                            operand: Integer_::createBy(
                                $reader->smallValue(),
                            )
                        ),

                        // NOTE: here is not implemented on actually the RubyVM.
                        // This is originally implemented by the RubyVM on PHP.
                        InsnType::TS_OFFSET => new Operand(
                            operand: (new Offset(new OffsetSymbol(
                                offset: $reader->smallValue(),
                            ))),
                        ),

                        InsnType::TS_IC => new Operand(
                            operand: $this->processInlineCache(
                                $reader->smallValue()
                            )
                        ),
                        InsnType::TS_ID, InsnType::TS_CDHASH => new Operand(
                            operand: $this->kernel->findId(
                                $reader->smallValue(),
                            ),
                        ),

                        // Not implemented yet
                        default => new Operand(
                            operand: Integer_::createBy($reader->smallValue()),
                        ),
                    },
                );
            }
        }

        return $entries;
    }

    private function loadCallInfoEntries(int $callInfoEntriesOffset, int $callInfoSize): CallInfoEntries
    {
        $reader = $this->kernel->stream()->duplication();

        $entries = new CallInfoEntries();
        $reader->pos($callInfoEntriesOffset);
        for ($i = 0; $i < $callInfoSize; ++$i) {
            $midIndex = $reader->smallValue();
            if ($midIndex === -1) {
                throw new InstructionSequenceProcessorException(
                    'Load call info getting unexpected value (mid: -1)',
                );
            }

            $mid = $this->kernel->findId($midIndex);
            $flag = $reader->smallValue();
            $argc = $reader->smallValue();

            $keywordLength = $reader->smallValue();

            $keywords = null;
            if ($keywordLength > 0) {
                $keywords = [];
                for ($j = 0; $j < $keywordLength; ++$j) {
                    $keyword = $reader->smallValue();
                    $keywords[] = $this->kernel
                        ->findObject($keyword);
                }
            }

            $entries->append(
                new CallInfo(
                    callData: new CallData(
                        mid: $mid,
                        flag: $flag,
                        argc: $argc,

                        // @phpstan-ignore-next-line
                        keywords: $keywords,
                    ),
                )
            );
        }

        return $entries;
    }

    private function loadOuterVariables(int $outerVariableOffset): OuterVariableEntries
    {
        $reader = $this->kernel->stream()->duplication();

        $entries = new OuterVariableEntries();
        $reader->pos($outerVariableOffset);

        $tableSize = $reader->smallValue();

        for ($i = 0; $i < $tableSize; ++$i) {
            $key = $this->kernel->findId($reader->smallValue());
            $value = $reader->smallValue();

            $entries[] = new OuterVariable(
                $key,
                $value,
            );
        }

        return $entries;
    }

    private function loadParamOptTable(int $paramOptTableOffset, int $paramOptNum): int
    {
        $reader = $this->kernel->stream()->duplication();
        $reader->pos($paramOptTableOffset);

        // TODO: implement here
        return -1;
    }

    private function loadKeyword(int $paramKeywordOffset): Keyword
    {
        return new Keyword();
    }

    private function loadInsnsBody(int $insnsInfoBodyOffset, int $insnsInfoSize): InsnsBodyEntries
    {
        return new InsnsBodyEntries();
    }

    private function loadInsnsPositions(int $insnsInfoBodyOffset, int $insnsInfoSize): InsnsPositionEntries
    {
        return new InsnsPositionEntries();
    }

    private function loadLocalTable(int $localTableOffset, int $localTableSize): VariableEntries
    {
        $reader = $this->kernel->stream()->duplication();
        $entries = new VariableEntries();
        $reader->pos($localTableOffset);

        for ($i = 0; $i < $localTableSize; ++$i) {
            // NOTE: only to read 4 bytes
            $entries[] = new VariableEntry($this->kernel->findId($reader->readAsUnsignedLong()));

            // TODO: You must using longlong but the PHP cannot read longlong value
            // You will rewrite here to enable to read by longlong
            $reader->readAsUnsignedLong();
        }

        return $entries;
    }

    private function loadCatchTable(int $catchTableOffset, int $catchTableSize): CatchEntries
    {
        $reader = $this->kernel->stream()->duplication();
        $reader->pos($catchTableOffset);

        $entries = new CatchEntries();

        for ($i = 0; $i < $catchTableSize; ++$i) {
            $entries[] = new Catch_(
                kernel: $this->kernel,
                iseqIndex: $reader->smallValue(),
                type: $reader->smallValue(),
                start: $reader->smallValue(),
                end: $reader->smallValue(),
                cont: $reader->smallValue(),
                sp: $reader->smallValue(),
            );
        }

        return $entries;
    }

    public function path(): string
    {
        return (string) $this->path;
    }

    private function processInlineCache(int $value): ID
    {
        return $this->kernel->findId($value);
    }

    /**
     * @return string[]
     */
    private function insnOperationsOperands(): array
    {
        return [
            "",     "\0", "LN",   "\0", "LN",   "\0", "LN",   "\0", "LN",   "\0",
            "LN",   "\0", "NN",   "\0", "N",    "\0", "IA",   "\0", "IA",   "\0",
            "IJ",   "\0", "IJ",   "\0", "K",    "\0", "I",    "\0", "I",    "\0",
            "I",    "\0", "I",    "\0", "",     "\0", "",     "\0", "V",    "\0",
            "N",    "\0", "V",    "\0", "N",    "\0", "",     "\0", "NN",   "\0",
            "",     "\0", "N",    "\0", "N",    "\0", "V",    "\0", "V",    "\0",
            "NN",   "\0", "",     "\0", "V",    "\0", "",     "\0", "N",    "\0",
            "N",    "\0", "",     "\0", "",     "\0", "N",    "\0", "",     "\0",
            "N",    "\0", "N",    "\0", "N",    "\0", "N",    "\0", "NVV",  "\0",
            "IAV",  "\0", "N",    "\0", "LL",   "\0", "N",    "\0", "ISN",  "\0",
            "IS",   "\0", "IS",   "\0", "CS",   "\0", "C",    "\0", "C",    "\0",
            "VC",   "\0", "C",    "\0", "VC",   "\0", "NI",   "\0", "CS",   "\0",
            "C",    "\0", "",     "\0", "N",    "\0", "O",    "\0", "O",    "\0",
            "O",    "\0", "O",    "\0", "ST",   "\0", "HO",   "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "CC",   "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "VC",   "\0", "VC",   "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "R",    "\0", "RN",   "\0",
            "RN",   "\0", "L",    "\0", "L",    "\0", "L",    "\0", "L",    "\0",
            "",     "\0", "",     "\0", "",     "\0", "LN",   "\0", "LN",   "\0",
            "LN",   "\0", "LN",   "\0", "LN",   "\0", "NN",   "\0", "N",    "\0",
            "IA",   "\0", "IA",   "\0", "IJ",   "\0", "IJ",   "\0", "K",    "\0",
            "I",    "\0", "I",    "\0", "I",    "\0", "I",    "\0", "",     "\0",
            "",     "\0", "V",    "\0", "N",    "\0", "V",    "\0", "N",    "\0",
            "",     "\0", "NN",   "\0", "",     "\0", "N",    "\0", "N",    "\0",
            "V",    "\0", "V",    "\0", "NN",   "\0", "",     "\0", "V",    "\0",
            "",     "\0", "N",    "\0", "N",    "\0", "",     "\0", "",     "\0",
            "N",    "\0", "",     "\0", "N",    "\0", "N",    "\0", "N",    "\0",
            "N",    "\0", "NVV",  "\0", "IAV",  "\0", "N",    "\0", "LL",   "\0",
            "N",    "\0", "ISN",  "\0", "IS",   "\0", "IS",   "\0", "CS",   "\0",
            "C",    "\0", "C",    "\0", "VC",   "\0", "C",    "\0", "VC",   "\0",
            "NI",   "\0", "CS",   "\0", "C",    "\0", "",     "\0", "N",    "\0",
            "O",    "\0", "O",    "\0", "O",    "\0", "O",    "\0", "ST",   "\0",
            "HO",   "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "CC",   "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "VC",   "\0", "VC",   "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "R",    "\0", "RN",   "\0", "RN",   "\0", "L",    "\0", "L",    "\0",
            "L",    "\0", "L",    "\0", "",     "\0", "",     "\0",
        ];
    }

    /**
     * @return int[]
     */
    private function insnOperationOperandsOffsets(): array
    {
        return [
            0,   1,   4,   7,  10,  13,  16,  19,  21,  24,  27,  30,
            33,  35,  37,  39,  41,  43,  44,  45,  47,  49,  51,  53,
            54,  57,  58,  60,  62,  64,  66,  69,  70,  72,  73,  75,
            77,  78,  79,  81,  82,  84,  86,  88,  90,  94,  98, 100,
            103, 105, 109, 112, 115, 118, 120, 122, 125, 127, 130, 133,
            136, 138, 139, 141, 143, 145, 147, 149, 152, 155, 157, 159,
            161, 163, 165, 167, 170, 172, 174, 176, 178, 180, 182, 184,
            186, 188, 191, 194, 196, 198, 200, 202, 204, 206, 208, 211,
            214, 216, 218, 220, 222, 223, 224, 225, 228, 231, 234, 237,
            240, 243, 245, 248, 251, 254, 257, 259, 261, 263, 265, 267,
            268, 269, 271, 273, 275, 277, 278, 281, 282, 284, 286, 288,
            290, 293, 294, 296, 297, 299, 301, 302, 303, 305, 306, 308,
            310, 312, 314, 318, 322, 324, 327, 329, 333, 336, 339, 342,
            344, 346, 349, 351, 354, 357, 360, 362, 363, 365, 367, 369,
            371, 373, 376, 379, 381, 383, 385, 387, 389, 391, 394, 396,
            398, 400, 402, 404, 406, 408, 410, 412, 415, 418, 420, 422,
            424, 426, 428, 430, 432, 435, 438, 440, 442, 444, 446, 447,
        ];
    }
}
