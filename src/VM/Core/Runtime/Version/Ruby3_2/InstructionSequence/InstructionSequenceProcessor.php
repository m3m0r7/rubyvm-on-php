<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\Runtime\Executor\CallInfoEntries;
use RubyVM\VM\Core\Runtime\Executor\CallInfoEntry;
use RubyVM\VM\Core\Runtime\Executor\Keyword;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationEntries;
use RubyVM\VM\Core\Runtime\Executor\OperationEntry;
use RubyVM\VM\Core\Runtime\Executor\UnknownEntry;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Insn\InsnType;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\Runtime\InstructionSequence\IDTable;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequenceBody;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequenceProcessorInterface;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\ObjectInfo;
use RubyVM\VM\Core\Runtime\Symbol\SymbolType;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry\CallData;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry\CatchEntries;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry\InsnsBodyEntries;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry\InsnsPositionEntries;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry\VariableEntries;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence\InstructionSequenceBody as Ruby3_2_InstructionSequenceBody;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Internal\Arithmetic;
use RubyVM\VM\Exception\ExecutorExeption;
use RubyVM\VM\Exception\InstructionSequenceProcessorException;
use RubyVM\VM\Stream\BinaryStreamReader;

class InstructionSequenceProcessor implements InstructionSequenceProcessorInterface
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly Aux $aux,
    ) {
        if (isset($this->kernel->instructionSequenceList[$this->aux->loader->index])) {
            return;
        }

        throw new InstructionSequenceProcessorException(
            sprintf(
                'Not found instructionSequenceList#%d',
                $this->aux->loader->index,
            ),
        );
    }

    public function process(): InstructionSequenceBody
    {
        /**
         * @var Offset $offset
         */
        $offset = $this->kernel->instructionSequenceList[$this->aux->loader->index];
        $this->kernel->stream()->pos($offset->offset);

        $calculateFromBodyOffset = fn (int $x) => $offset->offset - $x;

        $type = $this->kernel->stream()->smallValue();
        $iseqSize = $this->kernel->stream()->smallValue();

        $bytecodeOffset = $calculateFromBodyOffset($this->kernel->stream()->smallValue());
        $bytecodeSize = $this->kernel->stream()->smallValue();

        $paramFlags = $this->kernel->stream()->smallValue();
        $paramSize = $this->kernel->stream()->smallValue();
        $paramLeadNum = $this->kernel->stream()->smallValue();
        $paramOptNum = $this->kernel->stream()->smallValue();
        $paramRestStart = $this->kernel->stream()->smallValue();
        $paramPostStart = $this->kernel->stream()->smallValue();
        $paramPostNum = $this->kernel->stream()->smallValue();
        $paramBlockStart = $this->kernel->stream()->smallValue();
        $paramOptTableOffset = $calculateFromBodyOffset($this->kernel->stream()->smallValue());
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
        $insnsInfoBodyOffset = $calculateFromBodyOffset($this->kernel->stream()->smallValue());
        $insnsInfoPositionsOffset = $calculateFromBodyOffset($this->kernel->stream()->smallValue());
        $insnsInfoSize = $this->kernel->stream()->smallValue();
        $localTableOffset = $calculateFromBodyOffset($this->kernel->stream()->smallValue());
        $catchTableSize = $this->kernel->stream()->smallValue();
        $catchTableOffset = $calculateFromBodyOffset($this->kernel->stream()->smallValue());
        $parentISeqIndex = $this->kernel->stream()->smallValue();
        $localISeqIndex = $this->kernel->stream()->smallValue();
        $mandatoryOnlyIseqIndex = $this->kernel->stream()->smallValue();
        $ciEntriesOffset = $calculateFromBodyOffset($v = $this->kernel->stream()->smallValue());
        $outerVariablesOffset = $calculateFromBodyOffset($this->kernel->stream()->smallValue());
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

        $path = $this->kernel->findObject($locationPathObjIndex);

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
                        obj: 0,
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
                        obj: 0,
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
                        obj: 0,
                        index: $mandatoryOnlyIseqIndex,
                    ),
                ),
                processor: new self(
                    kernel: $this->kernel,
                    aux: $aux,
                ),
            )
            : null;

        $rbInstructionSequenceBody = new Ruby3_2_InstructionSequenceBody(
            type: $type,
            stackMax: $stackMax,
            iseqSize: $iseqSize,
            objectParam: new ObjectParameter(
                objectParamFlags: new ObjectParameterFlags(
                    hasLead:             (bool) (($paramFlags >> 0) & 1),
                    hasOpt:              (bool) (($paramFlags >> 1) & 1),
                    hasRest:             (bool) (($paramFlags >> 2) & 1),
                    hasPost:             (bool) (($paramFlags >> 3) & 1),
                    keyword:             $keyword,
                    hasKeyword:          (bool) (($paramFlags >> 4) & 1),
                    hasKeywordRest:      (bool) (($paramFlags >> 5) & 1),
                    hasBlock:            (bool) (($paramFlags >> 6) & 1),
                    ambiguousParam:      (bool) (($paramFlags >> 7) & 1),
                    acceptsNoKeywordArg: (bool) (($paramFlags >> 8) & 1),
                    ruby2Keywords:       (bool) (($paramFlags >> 9) & 1),
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
            localTable: $localTable,
            catchTable: $catchTable,
            parentISeq: $parentISeq,
            localISeq: $localISeq,
            mandatoryOnlyISeq: $mandatoryOnlyISeq,
            callInfoEntries: $callInfoEntries,
            bytecodeOffset: $bytecodeOffset,
            bytecodeSize: $bytecodeSize,
        );

        return new InstructionSequenceBody(
            $rbInstructionSequenceBody,
            $this->loadCode(
                bytecodeOffset: $bytecodeOffset,
                bytecodeSize: $bytecodeSize,
                instructionSequenceSize: $iseqSize,
                instructionSequenceBody: $rbInstructionSequenceBody,
            ),
        );
    }

    private function loadCode(
        int $bytecodeOffset,
        int $bytecodeSize,
        int $instructionSequenceSize,
        Ruby3_2_InstructionSequenceBody $instructionSequenceBody,
    ): OperationEntries {
        return $this->kernel->stream()->dryPosTransaction(
            function (BinaryStreamReader $reader) use ($bytecodeOffset, $bytecodeSize, $instructionSequenceSize, $instructionSequenceBody) {
                $entries = new OperationEntries();
                $operationMap = implode($this->insnOperations());
                $callInfoEntryIndex = 0;
                $reader->pos($bytecodeOffset);

                for ($codeIndex = 0; $codeIndex < $instructionSequenceSize;) {
                    $insn = Insn::of($insnValue = $reader->smallValue());
                    $entries->append(
                        new OperationEntry(
                            insn: $insn,
                        )
                    );


                    $types = $operationMap[$this->insnOperationOffsets()[$insnValue]] ?? null;
                    if ($types === null) {
                        throw new ExecutorExeption(
                            sprintf(
                                'Unknown INSN type: 0x%02x',
                                $insn,
                            )
                        );
                    }
                    $codeIndex++;

                    for ($opIndex = 0; ord($types[$opIndex] ?? "\0"); $opIndex++, $codeIndex++) {
                        $operandType = InsnType::of($types[$opIndex]);
                        $entries->append(
                            match ($operandType) {
                                InsnType::TS_VALUE => new OperandEntry(
                                    operand: $this->kernel
                                        ->findObject($reader->smallValue())
                                ),
                                InsnType::TS_CALLDATA => new OperandEntry(
                                    operand: $instructionSequenceBody
                                        ->callInfoEntries[$callInfoEntryIndex++],
                                ),
                                InsnType::TS_NUM,
                                InsnType::TS_LINDEX => new OperandEntry(
                                    operand: new Object_(
                                        info: new ObjectInfo(
                                            type: SymbolType::FIXNUM,
                                            specialConst: 1,
                                            frozen: 1,
                                            internal: 1,
                                        ),
                                        symbol: new NumberSymbol(
                                            Arithmetic::fix2int($reader->smallValue()),
                                        ),
                                    )
                                ),
                                // Not implemented yet
                                InsnType::TS_VARIABLE,
                                InsnType::TS_IVC,
                                InsnType::TS_IC,
                                InsnType::TS_ID,
                                InsnType::TS_ISE,
                                InsnType::TS_ISEQ,
                                InsnType::TS_FUNCPTR,
                                InsnType::TS_BUILTIN,
                                InsnType::TS_CDHASH,
                                InsnType::TS_ICVARC => throw new ExecutorExeption(
                                    sprintf(
                                        'The OperandType#%s is not supported',
                                        $operandType->name,
                                    ),
                                ),
                                default => new UnknownEntry(
                                    $reader->smallValue()
                                ),
                            },
                        );
                    }
                }

                return $entries;
            }
        );
    }

    private function loadCallInfoEntries(int $callInfoEntriesOffset, int $callInfoSize): CallInfoEntries
    {
        return $this->kernel->stream()->dryPosTransaction(
            function (BinaryStreamReader $reader) use ($callInfoEntriesOffset, $callInfoSize) {
                $entries = new CallInfoEntries();
                $reader->pos($callInfoEntriesOffset);
                for ($i = 0; $i < $callInfoSize; $i++) {
                    $midIndex = $reader->smallValue();
                    if ($midIndex === -1) {
                        $entries->append(new CallInfoEntry());
                        continue;
                    }
                    $mid = $this->kernel->findId($midIndex);
                    $flag = $reader->smallValue();
                    $argc = $reader->smallValue();

                    $keywordLength = $reader->smallValue();
                    $keywords = null;
                    if ($keywordLength > 0) {
                        $keywords = [];
                        for ($j = 0; $j < $keywordLength; $j++) {
                            $keywords[] = $this->kernel->findObject(
                                $reader->smallValue(),
                            );
                        }
                    }

                    $entries->append(
                        new CallInfoEntry(
                            callData: new CallData(
                                mid: $mid,
                                flag: $flag,
                                argc: $argc,
                                keywords: $keywords,
                            ),
                        )
                    );
                }
                return $entries;
            }
        );
    }

    private function loadOuterVariables(int $outerVariableOffset): VariableEntries
    {
        $entries = new VariableEntries();
        return $entries;
    }

    private function loadParamOptTable(int $paramOptTableOffset, int $paramOptNum): int
    {
        return -1;
    }

    private function loadKeyword(int $paramKeywordOffset): Keyword
    {
        return new Keyword();
    }

    private function loadInsnsBody(int $insnsInfoBodyOffset, int $insnsInfoSize): InsnsBodyEntries
    {
        $entries = new InsnsBodyEntries();
        return $entries;
    }

    private function loadInsnsPositions(int $insnsInfoBodyOffset, int $insnsInfoSize): InsnsPositionEntries
    {
        $entries = new InsnsPositionEntries();
        return $entries;
    }

    private function loadLocalTable(int $localTableOffset, int $localTableSize): VariableEntries
    {
        $entries = new VariableEntries();
        return $entries;
    }

    private function loadCatchTable(int $catchTableOffset, int $catchTableSize): CatchEntries
    {
        $entries = new CatchEntries();
        return $entries;
    }


    private function insnOperations(): array
    {
        return [
            "",     "\0", "LN",   "\0", "LN",   "\0", "LN",   "\0", "LN",   "\0",
            "LN",   "\0", "NN",   "\0", "N",    "\0", "IA",   "\0", "IA",   "\0",
            "IJ",   "\0", "IJ",   "\0", "K",    "\0", "I",    "\0", "I",    "\0",
            "I",    "\0", "I",    "\0", "",     "\0", "",     "\0", "V",    "\0",
            "N",    "\0", "V",    "\0", "N",    "\0", "",     "\0", "NN",   "\0",
            "",     "\0", "N",    "\0", "N",    "\0", "V",    "\0", "V",    "\0",
            "NN",   "\0", "",     "\0", "V",    "\0", "N",    "\0", "N",    "\0",
            "",     "\0", "",     "\0", "N",    "\0", "",     "\0", "N",    "\0",
            "N",    "\0", "N",    "\0", "N",    "\0", "NVV",  "\0", "N",    "\0",
            "LL",   "\0", "N",    "\0", "ISN",  "\0", "IS",   "\0", "IS",   "\0",
            "CS",   "\0", "C",    "\0", "C",    "\0", "VC",   "\0", "C",    "\0",
            "VC",   "\0", "N",    "\0", "N",    "\0", "CS",   "\0", "C",    "\0",
            "",     "\0", "N",    "\0", "O",    "\0", "O",    "\0", "O",    "\0",
            "O",    "\0", "ST",   "\0", "HO",   "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0", "CC",   "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0", "VC",   "\0",
            "VC",   "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "R",    "\0", "RN",   "\0", "RN",   "\0",
            "L",    "\0", "L",    "\0", "L",    "\0", "L",    "\0", "",     "\0",
            "",     "\0", "",     "\0", "LN",   "\0", "LN",   "\0", "LN",   "\0",
            "LN",   "\0", "LN",   "\0", "NN",   "\0", "N",    "\0", "IA",   "\0",
            "IA",   "\0", "IJ",   "\0", "IJ",   "\0", "K",    "\0", "I",    "\0",
            "I",    "\0", "I",    "\0", "I",    "\0", "",     "\0", "",     "\0",
            "V",    "\0", "N",    "\0", "V",    "\0", "N",    "\0", "",     "\0",
            "NN",   "\0", "",     "\0", "N",    "\0", "N",    "\0", "V",    "\0",
            "V",    "\0", "NN",   "\0", "",     "\0", "V",    "\0", "N",    "\0",
            "N",    "\0", "",     "\0", "",     "\0", "N",    "\0", "",     "\0",
            "N",    "\0", "N",    "\0", "N",    "\0", "N",    "\0", "NVV",  "\0",
            "N",    "\0", "LL",   "\0", "N",    "\0", "ISN",  "\0", "IS",   "\0",
            "IS",   "\0", "CS",   "\0", "C",    "\0", "C",    "\0", "VC",   "\0",
            "C",    "\0", "VC",   "\0", "N",    "\0", "N",    "\0", "CS",   "\0",
            "C",    "\0", "",     "\0", "N",    "\0", "O",    "\0", "O",    "\0",
            "O",    "\0", "O",    "\0", "ST",   "\0", "HO",   "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "CC",   "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "VC",   "\0", "VC",   "\0", "C",    "\0", "C",    "\0", "C",    "\0",
            "C",    "\0", "C",    "\0", "C",    "\0", "R",    "\0", "RN",   "\0",
            "RN",   "\0", "L",    "\0", "L",    "\0", "L",    "\0", "L",    "\0",
            "",     "\0", "",     "\0",
        ];
    }

    private function insnOperationOffsets(): array
    {
        return [
            0,   1,   4,   7,  10,  13,  16,  19,  21,  24,  27,  30,
            33,  35,  37,  39,  41,  43,  44,  45,  47,  49,  51,  53,
            54,  57,  58,  60,  62,  64,  66,  69,  70,  72,  74,  76,
            77,  78,  80,  81,  83,  85,  87,  89,  93,  95,  98, 100,
            104, 107, 110, 113, 115, 117, 120, 122, 125, 127, 129, 132,
            134, 135, 137, 139, 141, 143, 145, 148, 151, 153, 155, 157,
            159, 161, 163, 166, 168, 170, 172, 174, 176, 178, 180, 182,
            184, 187, 190, 192, 194, 196, 198, 200, 202, 204, 207, 210,
            212, 214, 216, 218, 219, 220, 221, 224, 227, 230, 233, 236,
            239, 241, 244, 247, 250, 253, 255, 257, 259, 261, 263, 264,
            265, 267, 269, 271, 273, 274, 277, 278, 280, 282, 284, 286,
            289, 290, 292, 294, 296, 297, 298, 300, 301, 303, 305, 307,
            309, 313, 315, 318, 320, 324, 327, 330, 333, 335, 337, 340,
            342, 345, 347, 349, 352, 354, 355, 357, 359, 361, 363, 365,
            368, 371, 373, 375, 377, 379, 381, 383, 386, 388, 390, 392,
            394, 396, 398, 400, 402, 404, 407, 410, 412, 414, 416, 418,
            420, 422, 424, 427, 430, 432, 434, 436, 438, 439,
        ];
    }
}
