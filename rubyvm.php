<?php
class RubyVMException extends RuntimeException
{
}

class RubyVMRaise extends RubyVMException
{

}

enum InsnType: string {
    case TS_VARIABLE = '.';
    case TS_CALLDATA = 'C';
    case TS_CDHASH = 'H';
    case TS_IC = 'K';
    case TS_IVC = 'A';
    case TS_ICVARC = 'J';
    case TS_ID = 'I';
    case TS_ISE = 'T';
    case TS_ISEQ = 'S';
    case TS_OFFSET = 'O';
    case TS_VALUE = 'V';
    case TS_LINDEX = 'L';
    case TS_FUNCPTR = 'F';
    case TS_NUM = 'N';
    case TS_BUILTIN = 'R';
};

enum RubyEncodingIndex {

    case RUBY_ENCINDEX_ASCII_8BIT;
    case RUBY_ENCINDEX_UTF_8;
    case RUBY_ENCINDEX_US_ASCII;

    /* preserved indexes */
    case RUBY_ENCINDEX_UTF_16BE;
    case RUBY_ENCINDEX_UTF_16LE;
    case RUBY_ENCINDEX_UTF_32BE;
    case RUBY_ENCINDEX_UTF_32LE;
    case RUBY_ENCINDEX_UTF_16;
    case RUBY_ENCINDEX_UTF_32;
    case RUBY_ENCINDEX_UTF8_MAC;

    /* for old options of regexp */
    case RUBY_ENCINDEX_EUC_JP;
    case RUBY_ENCINDEX_Windows_31J;
}

class ObjectHeader
{
    public function __construct(
        public readonly int $type,
        public readonly int $specialCount,
        public readonly int $frozen,
        public readonly int $internal,
    )
    {
    }
}

class ObjectParamFlags
{
    public function __construct(
        public readonly bool $hasLead,
        public readonly bool $hasOpt,
        public readonly bool $hasRest,
        public readonly bool $hasPost,
        public readonly ?string $keyword,
        public readonly bool $hasKeyword,
        public readonly bool $hasKeywordRest,
        public readonly bool $hasBlock,
        public readonly int $ambiguousParam,
        public readonly int $acceptsNoKeywordArg,
        public readonly int $ruby2Keywords,
    ) {
    }

    public function update(
        ?bool $hasLead = null,
        ?bool $hasOpt = null,
        ?bool $hasRest = null,
        ?bool $hasPost = null,
        ?string $keyword = null,
        ?bool $hasKeyword = null,
        ?bool $hasKeywordRest = null,
        ?bool $hasBlock = null,
        ?int $ambiguousParam = null,
        ?int $acceptsNoKeywordArg = null,
        ?int $ruby2Keywords = null,
    ): self {
        return new self(
            hasLead: $hasLead ?? $this->hasLead,
            hasOpt: $hasOpt ?? $this->hasOpt,
            hasRest: $hasRest ?? $this->hasRest,
            hasPost: $hasPost ?? $this->hasPost,
            keyword: $keyword ?? $this->keyword,
            hasKeyword: $hasKeyword ?? $this->hasKeyword,
            hasKeywordRest: $hasKeywordRest ?? $this->hasKeywordRest,
            hasBlock: $hasBlock ?? $this->hasBlock,
            ambiguousParam: $ambiguousParam ?? $this->ambiguousParam,
            acceptsNoKeywordArg: $acceptsNoKeywordArg ?? $this->acceptsNoKeywordArg,
            ruby2Keywords: $ruby2Keywords ?? $this->ruby2Keywords,
        );
    }
}

class ObjectParam
{
    public function __construct(
        public readonly ObjectParamFlags $objectParamFlags,
        public readonly int $size,
        public readonly int $leadNum,
        public readonly int $optNum,
        public readonly int $restStart,
        public readonly int $postStart,
        public readonly int $postNum,
        public readonly int $blockStart,
        public readonly mixed $optTable,
    )
    {
    }

    public function update(
        ?ObjectParamFlags $objectParamFlags = null,
        ?int $size = null,
        ?int $leadNum = null,
        ?int $optNum = null,
        ?int $restStart = null,
        ?int $postStart = null,
        ?int $postNum = null,
        ?int $blockStart = null,
        mixed $optTable = null,
    ): self {
        return new self(
            objectParamFlags: $objectParamFlags ?? $this->objectParamFlags,
            size: $size ?? $this->size,
            leadNum: $leadNum ?? $this->leadNum,
            optNum: $optNum ?? $this->optNum,
            restStart: $restStart ?? $this->restStart,
            postStart: $postStart ?? $this->postStart,
            postNum: $postNum ?? $this->postNum,
            blockStart: $blockStart ?? $this->blockStart,
            optTable: $optTable ?? $this->optTable,
        );
    }
}

class Insns
{
    public function __construct(
        // load_body->insns_info.size
        public readonly int $size,
        // load_body->insns_info.body
        public readonly mixed $body,
        // load_body->insns_info.positions
        public readonly mixed $positions,
    ){
    }

    public function update(
        // load_body->insns_info.size
        ?int $size = null,
        // load_body->insns_info.body
        mixed $body = null,
        // load_body->insns_info.positions
        mixed $positions = null,
    ): self {
        return new self(
            size: $size ?? $this->size,
            body: $body ?? $this->body,
            positions: $positions ?? $this->positions,
        );
    }
}

class Variable
{
    public function __construct(
        public readonly int $flipCount,
        public readonly mixed $scriptLines,
    ){
    }

    public function update(
        ?int $flipCount = null,
        mixed $scriptLines = null,
    ): self {
        return new self(
            flipCount: $flipCount ?? $this->flipCount,
            scriptLines: $scriptLines ?? $this->scriptLines,
        );
    }
}

class CodePosition
{
    public function __construct(
        public readonly int $lineNumber,
        public readonly int $column,
    ){
    }
}

class CodeLocation
{
    public function __construct(
        public readonly CodePosition $begin,
        public readonly CodePosition $end,
    ){
    }
}

class Location
{
    public function __construct(
        public readonly int $firstLineNo,
        public readonly int $nodeId,
        public readonly CodeLocation $codeLocation,
    ){
    }
}


class ObjectBody
{
    public function __construct(
        public readonly int $type,
        public readonly int $stackMax,
        public readonly ObjectParam $objectParam,
        public readonly int $localTableSize,
        public readonly int $ciSize,
        public readonly Insns $insns,
        public readonly Variable $variable,
        public readonly Location $location,
        public readonly int $catchExceptP,
        public readonly int $builtinInlineP,
        public readonly int $ivcSize,
        public readonly int $icvArcSize,
        public readonly int $iseSize,
        public readonly int $icSize,
        public readonly mixed $isEntries,
        public readonly mixed $outerVariables,
        public readonly mixed $localTable,
        public readonly mixed $catchTable,
        public readonly mixed $parentISeq,
        public readonly mixed $localISeq,
        public readonly mixed $mandatoryOnlyISeq,
        public readonly ?CallInfoEntries $callInfoEntries,
    )
    {
    }

    public function update(
        ?int $type = null,
        ?int $stackMax = null,
        ?ObjectParam $objectParam = null,
        ?int $localTableSize = null,
        ?int $ciSize = null,
        ?Insns $insns = null,
        ?Variable $variable = null,
        ?Location $location = null,
        ?int $catchExceptP = null,
        ?int $builtinInlineP = null,
        ?int $ivcSize = null,
        ?int $icvArcSize = null,
        ?int $iseSize = null,
        ?int $icSize = null,
        mixed $isEntries = null,
        mixed $outerVariables = null,
        mixed $localTable = null,
        mixed $catchTable = null,
        mixed $parentISeq = null,
        mixed $localISeq = null,
        mixed $mandatoryOnlyISeq = null,
        ?CallInfoEntries $callInfoEntries = null,
    ): self {
        return new self(
            type: $type ?? $this->type,
            stackMax: $stackMax ?? $this->stackMax,
            objectParam: $objectParam ?? $this->objectParam,
            localTableSize: $localTableSize ?? $this->localTableSize,
            ciSize: $ciSize ?? $this->ciSize,
            insns: $insns ?? $this->insns,
            variable: $variable ?? $this->variable,
            location: $location ?? $this->location,
            catchExceptP: $catchExceptP ?? $this->catchExceptP,
            builtinInlineP: $builtinInlineP ?? $this->builtinInlineP,
            ivcSize: $ivcSize ?? $this->ivcSize,
            icvArcSize: $icvArcSize ?? $this->icvArcSize,
            iseSize: $iseSize ?? $this->iseSize,
            icSize: $icSize ?? $this->icSize,
            isEntries: $isEntries ?? $this->isEntries,
            outerVariables: $outerVariables ?? $this->outerVariables,
            localTable: $localTable ?? $this->localTableSize,
            catchTable: $catchTable ?? $this->catchTable,
            parentISeq: $parentISeq ?? $this->parentISeq,
            localISeq: $localISeq ?? $this->localISeq,
            mandatoryOnlyISeq: $mandatoryOnlyISeq ?? $this->mandatoryOnlyISeq,
            callInfoEntries: $callInfoEntries ?? $this->callInfoEntries,
        );
    }
}

class ID
{
    public readonly int $value;

    public function __construct(public readonly mixed $symbol)
    {
        $this->value = spl_object_id($this);
    }
}

class Symbol
{
    public readonly ID $id;
    public function __construct(
        public readonly int $offset,
        public readonly string $loaderType,
        public readonly ObjectHeader $header,
        public readonly mixed $symbol,
    )
    {
        $this->id = new ID($this->symbol);
    }
}

trait Collectable
{
    protected array $items = [];

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? throw new RubyVMException("Out of bound in the ID Table: {$offset}");
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$this->valid($value)) {
            throw new RubyVMException('The value is not accepted');
        }
        $this->items[$offset ?? count($this->items)] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if (isset($this->items[$offset])) {
            unset($this->items[$offset]);
        }
    }

    public function count(): int
    {
        return count($this->items);
    }
}

class IDTable implements ArrayAccess, Countable
{
    use Collectable;

    public function valid(mixed $value): bool
    {
        return is_int($value);
    }
}

class Keyword
{

}

interface EntryInterface
{
}

class InsnsBodyEntry implements EntryInterface
{
    public function __construct(
        public readonly int $lineNo,
        public readonly int $nodeId,
        public readonly int $events,
    )
    {

    }
}

class CatchTableEntry implements EntryInterface
{
    public function __construct(
        public readonly int $type,
        public readonly int $start,
        public readonly int $end,
        public readonly int $cont,
        public readonly int $sp,
        public readonly ISeq $iseq,
    )
    {

    }
}

class Entries implements ArrayAccess, Countable
{
    use Collectable;

    public function valid(mixed $value): bool
    {
        return $value instanceof EntryInterface;
    }
}

class Positions implements ArrayAccess, Countable
{
    use Collectable;

    public function valid(mixed $value): bool
    {
        return is_int($value);
    }
}

class CallCache
{

}

class CallInfo
{
    public function __construct(
        public readonly int $flags,
        public readonly ?array $keywords,
        public readonly mixed $mid,
        public readonly int $flag,
        public readonly int $argc,
    )
    {
    }
}

class CallData
{
    public function __construct(
        public readonly ?CallInfo $callInfo = null,
        public readonly ?CallCache $callCache = null,
    )
    {
    }

    public function update(
        CallInfo $callInfo = null,
        CallCache $callCache = null,
    )
    {
    }
}

class CallInfoEntries implements ArrayAccess, Countable
{
    use Collectable;

    public function valid(mixed $value): bool
    {
        return $value instanceof CallData;
    }
}

class AuxLoader
{
    public function __construct(
        public readonly int $obj,
        public readonly int $index,
    ){
    }

}

class Aux
{
    public function __construct(
        public readonly AuxLoader $loader,
    ){

    }
}

class ISeq
{
    private array $mnemonics = [
        'nop',
        'getlocal',
        'setlocal',
        'getblockparam',
        'setblockparam',
        'getblockparamproxy',
        'getspecial',
        'setspecial',
        'getinstancevariable',
        'setinstancevariable',
        'getclassvariable',
        'setclassvariable',
        'opt_getconstant_path',
        'getconstant',
        'setconstant',
        'getglobal',
        'setglobal',
        'putnil',
        'putself',
        'putobject',
        'putspecialobject',
        'putstring',
        'concatstrings',
        'anytostring',
        'toregexp',
        'intern',
        'newarray',
        'newarraykwsplat',
        'duparray',
        'duphash',
        'expandarray',
        'concatarray',
        'splatarray',
        'newhash',
        'newrange',
        'pop',
        'dup',
        'dupn',
        'swap',
        'opt_reverse',
        'topn',
        'setn',
        'adjuststack',
        'defined',
        'checkmatch',
        'checkkeyword',
        'checktype',
        'defineclass',
        'definemethod',
        'definesmethod',
        'send',
        'opt_send_without_block',
        'objtostring',
        'opt_str_freeze',
        'opt_nil_p',
        'opt_str_uminus',
        'opt_newarray_max',
        'opt_newarray_min',
        'invokesuper',
        'invokeblock',
        'leave',
        'throw',
        'jump',
        'branchif',
        'branchunless',
        'branchnil',
        'once',
        'opt_case_dispatch',
        'opt_plus',
        'opt_minus',
        'opt_mult',
        'opt_div',
        'opt_mod',
        'opt_eq',
        'opt_neq',
        'opt_lt',
        'opt_le',
        'opt_gt',
        'opt_ge',
        'opt_ltlt',
        'opt_and',
        'opt_or',
        'opt_aref',
        'opt_aset',
        'opt_aset_with',
        'opt_aref_with',
        'opt_length',
        'opt_size',
        'opt_empty_p',
        'opt_succ',
        'opt_not',
        'opt_regexpmatch2',
        'invokebuiltin',
        'opt_invokebuiltin_delegate',
        'opt_invokebuiltin_delegate_leave',
        'getlocal_WC_0',
        'getlocal_WC_1',
        'setlocal_WC_0',
        'setlocal_WC_1',
        'putobject_INT2FIX_0_',
        'putobject_INT2FIX_1_',
        'trace_nop',
        'trace_getlocal',
        'trace_setlocal',
        'trace_getblockparam',
        'trace_setblockparam',
        'trace_getblockparamproxy',
        'trace_getspecial',
        'trace_setspecial',
        'trace_getinstancevariable',
        'trace_setinstancevariable',
        'trace_getclassvariable',
        'trace_setclassvariable',
        'trace_opt_getconstant_path',
        'trace_getconstant',
        'trace_setconstant',
        'trace_getglobal',
        'trace_setglobal',
        'trace_putnil',
        'trace_putself',
        'trace_putobject',
        'trace_putspecialobject',
        'trace_putstring',
        'trace_concatstrings',
        'trace_anytostring',
        'trace_toregexp',
        'trace_intern',
        'trace_newarray',
        'trace_newarraykwsplat',
        'trace_duparray',
        'trace_duphash',
        'trace_expandarray',
        'trace_concatarray',
        'trace_splatarray',
        'trace_newhash',
        'trace_newrange',
        'trace_pop',
        'trace_dup',
        'trace_dupn',
        'trace_swap',
        'trace_opt_reverse',
        'trace_topn',
        'trace_setn',
        'trace_adjuststack',
        'trace_defined',
        'trace_checkmatch',
        'trace_checkkeyword',
        'trace_checktype',
        'trace_defineclass',
        'trace_definemethod',
        'trace_definesmethod',
        'trace_send',
        'trace_opt_send_without_block',
        'trace_objtostring',
        'trace_opt_str_freeze',
        'trace_opt_nil_p',
        'trace_opt_str_uminus',
        'trace_opt_newarray_max',
        'trace_opt_newarray_min',
        'trace_invokesuper',
        'trace_invokeblock',
        'trace_leave',
        'trace_throw',
        'trace_jump',
        'trace_branchif',
        'trace_branchunless',
        'trace_branchnil',
        'trace_once',
        'trace_opt_case_dispatch',
        'trace_opt_plus',
        'trace_opt_minus',
        'trace_opt_mult',
        'trace_opt_div',
        'trace_opt_mod',
        'trace_opt_eq',
        'trace_opt_neq',
        'trace_opt_lt',
        'trace_opt_le',
        'trace_opt_gt',
        'trace_opt_ge',
        'trace_opt_ltlt',
        'trace_opt_and',
        'trace_opt_or',
        'trace_opt_aref',
        'trace_opt_aset',
        'trace_opt_aset_with',
        'trace_opt_aref_with',
        'trace_opt_length',
        'trace_opt_size',
        'trace_opt_empty_p',
        'trace_opt_succ',
        'trace_opt_not',
        'trace_opt_regexpmatch2',
        'trace_invokebuiltin',
        'trace_opt_invokebuiltin_delegate',
        'trace_opt_invokebuiltin_delegate_leave',
        'trace_getlocal_WC_0',
        'trace_getlocal_WC_1',
        'trace_setlocal_WC_0',
        'trace_setlocal_WC_1',
        'trace_putobject_INT2FIX_0_',
        'trace_putobject_INT2FIX_1_',
    ];
    public function __construct(
        public readonly Aux $aux,
        public readonly ?ISeqData $data,
    ) {
    }

    public function update(
        ?Aux $aux = null,
        ?ISeqData $data = null,
    ) {
        return new self(
            aux: $aux ?? $this->aux,
            data: $data ?? $this->data,
        );
    }

    public function evaluate()
    {
        $stacks = [];
        // Here is vm.inc in Ruby
        for ($opCode = 0; $opCode < count($this->data->code); $opCode++) {
            $mnemonic = $this->mnemonics[$this->data->code[$opCode]];
            // TODO: Very ultra super experimental implementation. I will rewrite this VM implementation with PHP ecosystem's.
            switch ($mnemonic) {
                case 'putself':
                    $stacks[] = $this;
                    break;
                case 'putstring':
                    $opCode++;
                    $stacks[] = $this->data->code[$opCode];
                    break;
                case 'opt_send_without_block':
                    $opCode++;
                    /**
                     * @var CallData $operand
                     */
                    $operand = $this->data->code[$opCode];

                    // TODO: Move stack pointer (using $operand->callInfo->argc)
                    $val = null;
                    $recv = $stacks[count($stacks) - $operand->callInfo->argc - 1];
                    $recv2 = $stacks[count($stacks) - 0 - 1];

                    /**
                     * @var Symbol $mid
                     */
                    $mid = $operand->callInfo->mid;
                    $stacks[] = $recv->{$mid->symbol}($recv2);
                    break;
                case 'leave':
                    // Nothing to do.
                    break;
                case 'nop':
                    break;
                default:
                    throw new RubyVMException("🐈 < {$mnemonic}");
            }
        }
    }

    public function puts(Symbol $symbol)
    {
        echo $symbol->symbol;
    }
}

class ISeqData
{
    public function __construct(
        public readonly ObjectBody $objectBody,
        public readonly array $code,
    ) {

    }
}

class RubyVM
{
    const RUBY_ENCINDEX_BUILTIN_MAX = 12;

    protected string $magic;
    protected int $majorVersion;
    protected int $minorVersion;
    protected int $fileSize;
    protected int $extraSize;
    protected int $iSeqListSize;
    protected int $globalObjectListSize;
    protected int $iSeqListOffset;
    protected int $globalObjectListOffset;

    protected array $iSeqPosList = [];
    protected array $objectList = [];

    protected array $iseqList = [];

    protected array $loadedObject = [];

    protected array $operationCodes = [
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

    protected array $operationOffsets = [
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

    protected array $objectFunctions = [
        'loadObjectUnsupported', /* T_NONE */
        'loadObjectUnsupported', /* T_OBJECT */
        'loadObjectClass',       /* T_CLASS */
        'loadObjectUnsupported', /* T_MODULE */
        'loadObjectFloat',       /* T_FLOAT */
        'loadObjectString',      /* T_STRING */
        'loadObjectRegexp',      /* T_REGEXP */
        'loadObjectArray',       /* T_ARRAY */
        'loadObjectHash',        /* T_HASH */
        'loadObjectStruct',      /* T_STRUCT */
        'loadObjectBignum',      /* T_BIGNUM */
        'loadObjectUnsupported', /* T_FILE */
        'loadObjectData',        /* T_DATA */
        'loadObjectUnsupported', /* T_MATCH */
        'loadObjectComplexRational', /* T_COMPLEX */
        'loadObjectComplexRational', /* T_RATIONAL */
        'loadObjectUnsupported', /* 0x10 */
        'loadObjectUnsupported', /* T_NIL */
        'loadObjectUnsupported', /* T_TRUE */
        'loadObjectUnsupported', /* T_FALSE */
        'loadObjectSymbol',
        'loadObjectUnsupported', /* T_FIXNUM */
        'loadObjectUnsupported', /* T_UNDEF */
        'loadObjectUnsupported', /* 0x17 */
        'loadObjectUnsupported', /* 0x18 */
        'loadObjectUnsupported', /* 0x19 */
        'loadObjectUnsupported', /* T_IMEMO 0x1a */
        'loadObjectUnsupported', /* T_NODE 0x1b */
        'loadObjectUnsupported', /* T_ICLASS 0x1c */
        'loadObjectUnsupported', /* T_ZOMBIE 0x1d */
        'loadObjectUnsupported', /* 0x1e */
        'loadObjectUnsupported', /* 0x1f */
    ];

    public function __construct(protected readonly Stream $stream)
    {
    }

    protected function setup(): self
    {
        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L11087
        $this->magic = $this->stream->read(4);
        $this->majorVersion = $this->stream->readUnsignedLong();
        $this->minorVersion = $this->stream->readUnsignedLong();
        $this->fileSize = $this->stream->readUnsignedLong();
        $this->extraSize = $this->stream->readUnsignedLong();
        $this->iSeqListSize = $this->stream->readUnsignedLong();
        $this->globalObjectListSize = $this->stream->readUnsignedLong();

        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L11096C5-L11096C17
        $this->iSeqListOffset = $this->stream->readUnsignedLong();

        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L11097
        $this->globalObjectListOffset = $this->stream->readUnsignedLong();

        return $this;
    }

    protected function loadISeqEach(ISeq $iseq): ISeqData
    {
        $offset = $this->iSeqPosList[$iseq->aux->loader->index];
        $this->stream->move($offset);

        $calculateFromBodyOffset = fn (int $x) => $offset - $x;

        $type = $this->readSmallValue();
        $iseqSize = $this->readSmallValue();

        $bytecodeOffset = $calculateFromBodyOffset($this->readSmallValue());
        $bytecodeSize = $this->readSmallValue();

        $paramFlags = $this->readSmallValue();
        $paramSize = $this->readSmallValue();
        $paramLeadNum = $this->readSmallValue();
        $paramOptNum = $this->readSmallValue();
        $paramRestStart = $this->readSmallValue();
        $paramPostStart = $this->readSmallValue();
        $paramPostNum = $this->readSmallValue();
        $paramBlockStart = $this->readSmallValue();
        $paramOptTableOffset = $calculateFromBodyOffset($this->readSmallValue());
        $paramKeywordOffset = $this->readSmallValue();
        $locationPathObjIndex = $this->readSmallValue();
        $locationBaseLabelIndex = $this->readSmallValue();
        $locationLabelIndex = $this->readSmallValue();
        $locationFirstLineNo = $this->readSmallValue();
        $locationNodeId = $this->readSmallValue();
        $locationCodeLocationBegPosLineNo = $this->readSmallValue();
        $locationCodeLocationBegPosColumn = $this->readSmallValue();
        $locationCodeLocationEndPosLineNo = $this->readSmallValue();
        $locationCodeLocationEndPosColumn = $this->readSmallValue();
        $insnsInfoBodyOffset = $calculateFromBodyOffset($this->readSmallValue());
        $insnsInfoPositionsOffset = $calculateFromBodyOffset($this->readSmallValue());
        $insnsInfoSize = $this->readSmallValue();
        $localTableOffset = $calculateFromBodyOffset($this->readSmallValue());
        $catchTableSize = $this->readSmallValue();
        $catchTableOffset = $calculateFromBodyOffset($this->readSmallValue());
        $parentISeqIndex = $this->readSmallValue();
        $localISeqIndex = $this->readSmallValue();
        $mandatoryOnlyIseqIndex = $this->readSmallValue();
        $ciEntriesOffset = $calculateFromBodyOffset($v = $this->readSmallValue());
        $outerVariablesOffset = $calculateFromBodyOffset($this->readSmallValue());
        $variableFlipCount = $this->readSmallValue();
        $localTableSize = $this->readSmallValue();

        $ivcSize = $this->readSmallValue();
        $icvArcSize = $this->readSmallValue();
        $iseSize = $this->readSmallValue();
        $icSize = $this->readSmallValue();

        $ciSize = $this->readSmallValue();
        $stackMax = $this->readSmallValue();

        $catchExceptP = $this->readSmallValue();

        // TODO: Will change here
        $builtinInlineP = $this->readSmallValue();

        $path = $this->loadObject($locationPathObjIndex);

        $objectBody = new ObjectBody(
            type: $type,
            stackMax: $stackMax,
            objectParam: $objectParam = new ObjectParam(
                objectParamFlags: $objectParamFlag = new ObjectParamFlags(
                    hasLead:             (bool) (($paramFlags >> 0) & 1),
                    hasOpt:              (bool) (($paramFlags >> 1) & 1),
                    hasRest:             (bool) (($paramFlags >> 2) & 1),
                    hasPost:             (bool) (($paramFlags >> 3) & 1),
                    keyword:             null,
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
                optTable: null,
            ),
            localTableSize: $localTableSize,
            ciSize: $ciSize,
            insns: $insns = new Insns(
                size: $insnsInfoSize,
                body: null,
                positions: null,
            ),
            variable: $variable = new Variable(
                flipCount: $variableFlipCount,
                scriptLines: null,
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
            isEntries: null,
            outerVariables: null,
            localTable: null,
            catchTable: null,
            parentISeq: null,
            localISeq: null,
            mandatoryOnlyISeq: null,
            callInfoEntries: null,
        );

        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L12298
        $callInfoEntries = $this->loadCallInfoEntries($ciEntriesOffset, $ciSize, $objectBody);
        $outerVariables = $this->loadOuterVariables($outerVariablesOffset);
        $paramOptTable = $this->loadParamOptTable($paramOptTableOffset, $paramOptNum);

        $keyword = $this->loadKeyword($paramKeywordOffset);
        $insnsBody = $this->loadInsnsBody($insnsInfoBodyOffset, $insnsInfoSize);
        $insnsPositions = $this->loadInsnsPositions($insnsInfoBodyOffset, $insnsInfoSize);
        $localTable = $this->loadLocalTable($localTableOffset, $localTableSize);
        $catchTable = $this->loadCatchTable($catchTableOffset, $catchTableSize);
        $parentISeq = $this->loadISeq($parentISeqIndex);
        $localISeq = $this->loadISeq($localISeqIndex);
        $mandatoryOnlyISeq = $this->loadISeq($mandatoryOnlyIseqIndex);

        $objectBody = $objectBody->update(
            objectParam: $objectParam->update(
                objectParamFlags: $objectParamFlag->update(
                    keyword:$keyword,
                ),
                optTable: $paramOptTable,
            ),
            insns: $insns->update(
                body: $insnsBody,
                positions: $insnsPositions,
            ),
            variable: $variable->update(
                scriptLines: null, // TODO: Implement here
            ),
            isEntries: null, // TODO: Implement here
            outerVariables: $outerVariables,
            localTable: $localTable,
            catchTable: $catchTable,
            parentISeq: $parentISeq,
            localISeq: $localISeq,
            mandatoryOnlyISeq: $mandatoryOnlyISeq,
            callInfoEntries: $callInfoEntries,
        );

        return new ISeqData(
            $objectBody,
            $this->loadCode(
                $objectBody,
                $bytecodeOffset,
                $bytecodeSize,
                $iseqSize,
            ),
        );
    }

    public function loadObject(int $index): ?Symbol
    {
        if ($index === 0) {
            return null;
        }
        if (isset($this->loadedObject[$index])) {
            return $this->loadedObject[$index];
        }
        $currentPos = $this->stream->pos();

        $offset = $this->objectList[$index];
        $objectHeader = $this->loadObjectHeader($offset);

        $this->loadedObject[$index] = new Symbol(
            offset: $offset,
            loaderType: $this->objectFunctions[$objectHeader->type],
            header: $objectHeader,
            symbol: $this->{$this->objectFunctions[$objectHeader->type]}($offset + 1, $objectHeader),
        );

        $this->stream->move($currentPos);
        return $this->loadedObject[$index];
    }

    private function loadOuterVariables(int $outerVariableOffset): IDTable
    {
        $this->stream->move($outerVariableOffset);
        $tableSize = $this->readSmallValue();

        if ($tableSize > 0) {
            throw new RubyVMException("Not implemented yet \$tableSize is greater than zero (value is: {$tableSize})");
        }

        $IDTable = new IDTable();

        for ($i = 0; $i < $tableSize; $i++) {
            $key = $this->loadId($this->readSmallValue());
            $value = $this->readSmallValue();
            $IDTable[$key] = $value;
        }

        return $IDTable;
    }

    private function loadCallInfoEntries(int $ciEntriesOffset, int $ciSize, ObjectBody $objectBody): CallInfoEntries
    {
        $this->stream->move($ciEntriesOffset);
        if ($ciSize !== 1) {
            throw new RubyVMException('The loadCallInfoEntries not implemented yet; because `RubyVM on PHP` is very experimental project. The test is required $ciSize to be 1 (current value: ' . $ciSize . ')');
        }

        $callInfoEntries = new CallInfoEntries();

        for ($i = 0; $i < $ciSize; $i++) {
            $midIndex = $this->readSmallValue();

            if ($midIndex === -1) {
                $callInfoEntries[] = new CallData();
                continue;
            }

            // TODO: I will change searchable method by ID, but this RubyVM is very ultra super experimental implementation; thus to use loadObject temporarily.
            // $mid = $this->loadId($midIndex);
            $mid = $this->loadObject($midIndex);
            $flag = $this->readSmallValue();
            $argc = $this->readSmallValue();

            $len = $this->readSmallValue();
            $keywords = null;
            if ($len > 0) {
                $keyword = [];
                for ($j = 0; $j < $len; $j++) {
                    $keywords[] = $this->loadObject(
                        $this->readSmallValue()
                    );
                }
            }

            $callInfoEntries[] = new CallData(
                callInfo: new CallInfo(
                    flags: $flag, // TODO: What is here?
                    keywords: $keywords,
                    mid: $mid,
                    flag: $flag,
                    argc: $argc,
                ),
            );

        }

        return $callInfoEntries;
    }

    private function loadId(int $index): int
    {
        if ($index === 0) {
            return 0;
        }
        return $this->loadObject($index)
            ->id
            ->value;
    }

    private function loadObjectHeader(int $offset): ObjectHeader
    {
        $pos = $this->stream->pos();
        $this->stream->move($offset);
        $byte = $this->stream->readByte();
        return new ObjectHeader(
            type:         ($byte >> 0) & 0x1f,
            specialCount: ($byte >> 5) & 0x01,
            frozen:       ($byte >> 6) & 0x01,
            internal:     ($byte >> 7) & 0x01,
        );
    }

    private function readSmallValue(): int
    {
        $offset = $this->stream->pos();

        // Emulates: rb_popcount32(uint32_t x)
        $ntzInt32 = function (int $x): int {
            $x = ~$x & ($x-1);
            $x = ($x & 0x55555555) + ($x >> 1 & 0x55555555);
            $x = ($x & 0x33333333) + ($x >> 2 & 0x33333333);
            $x = ($x & 0x0f0f0f0f) + ($x >> 4 & 0x0f0f0f0f);
            $x = ($x & 0x001f001f) + ($x >> 8 & 0x001f001f);
            $x = ($x & 0x0000003f) + ($x >>16 & 0x0000003f);
            return $x;
        };
        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L11299
        $c = $this->stream->readUnsignedByte();

        $n = ($c & 1)
            ? 1
            : ($c == 0 ? 9 : $ntzInt32($c) + 1);

        $x = $c >> $n;

        if ($x === 0x7f) {
            $x = 1;
        }
        for ($i = 1; $i < $n; $i++) {
            $x <<= 8;
            $x |= $this->stream->value(
                $offset + $i,
            );
        }

        $this->stream->move(
            $offset + $n,
        );

        return $x;
    }

    public function disassemble(): ISeq
    {
        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L13134
        $this->setup();

        $this->stream->move($this->globalObjectListOffset);
        $this->objectList = array_values(
            unpack('V*', $v = $this->stream->read($this->globalObjectListSize * SizeOf::LONG))
        );

        // Move stream to iseq list
        $this->stream->move($this->iSeqListOffset);
        $this->iSeqPosList = array_values(
            unpack('C*', $this->stream->read($this->iSeqListSize))
        );

        $iseq = $this->loadISeq(0);

        if ($iseq === null) {
            throw new RubyVMException('The ISeq is null');
        }

        return $iseq;
    }

    public function __debugInfo(): array
    {
        return [
            'Compiled Ruby Version' => "{$this->majorVersion}.{$this->minorVersion}",
            "YARB file" => ($this->magic === 'YARB' ? 'YES' : 'NO'),
            "File size" => $this->fileSize,
            "Extra size" => $this->extraSize,
            "ISeqListSize" => $this->iSeqListSize,
            "GlobalObjectListSize" => $this->globalObjectListSize,
            "ISeqListOffset" => $this->iSeqListOffset,
            "GlobalObjectListOffset" => $this->globalObjectListOffset,
        ];
    }

    private function loadObjectString(int $offset, ObjectHeader $objectHeader): string
    {
        // move
        $this->stream->move($offset);

        $encIndex = $this->readSmallValue();
        $len = $this->readSmallValue();

        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L12567
        if ($encIndex > static::RUBY_ENCINDEX_BUILTIN_MAX) {
            throw new RubyVMException('Not implemented yet in encIndex > RUBY_ENCINDEX_BUILTIN_MAX comparison');
        }

        /* NOTE: No required imitating from the Ruby main code; because it is calculating wide characters itself but PHP can calculate it automatically */
        return $this->readString(
            $len,
            /* TODO: You should switch encoding automatically by $encIndex; Here implementation is temporarily */
            RubyEncodingIndex::RUBY_ENCINDEX_UTF_8,
        );
    }

    private function loadObjectSymbol(int $offset, ObjectHeader $objectHeader)
    {
        /* This is same of load object string */
        return $this->loadObjectString($offset, $objectHeader);
    }

    private function loadParamOptTable(int $offset, int $num): int
    {
        $this->stream->move($offset);
        $table = array_fill(0, SizeOf::LONG, 0);
        for ($i = 0; $i < ($num + 1); $i++) {
            $table[SizeOf::LONG - ($i + 1)] = ord($this->stream->read($i + 1));
        }

        return ($table[0] << 24) + ($table[1] << 16) + ($table[2] << 8) + $table[3];
    }

    private function loadKeyword(int $offset): ?Keyword
    {
        if (!$offset) {
            return null;
        }
        /* TODO: Implement here */
        $this->stream->move($offset);

        // Keyword structure is here:
        // const struct rb_iseq_param_keyword {
        // int num;
        // int required_num;
        // int bits_start;
        // int rest_start;
        // const ID *table;
        // VALUE *default_values;
        // } *keyword;
        $num = $this->stream->readLong();
        $requiredNum = $this->stream->readLong();
        $bitsStart = $this->stream->readLong();
        $restStart = $this->stream->readLong();
        $table = $this->stream->readUnsignedLong();
        $defaultValue = $this->stream->readUnsignedLong();

        return new Keyword();
    }

    private function loadInsnsBody(int $offset, int $size): Entries
    {
        $this->stream->move($offset);
        $entries = new Entries();

        for ($i = 0; $i < $size; $i++) {
            $entries[] = new InsnsBodyEntry(
                lineNo: $this->readSmallValue(),
                nodeId: $this->readSmallValue(),
                events: $this->readSmallValue(),
            );
        }
        return $entries;
    }

    private function loadInsnsPositions(int $offset, int $size): Positions
    {
        $this->stream->move($offset);
        $positions = new Positions();
        $last = 0;
        for ($i = 0; $i < $size; $i++) {
            $positions[] = $last = $last + $this->readSmallValue();
        }

        return $positions;
    }

    private function loadLocalTable(int $offset, int $size): ?IDTable
    {
        if ($size <= 0) {
            return null;
        }
        $this->stream->move($offset);
        $table = new IDTable();

        for ($i = 0; $i < $size; $i++) {
            $table[] = $this->loadId($this->stream->readUnsignedByte());
        }

        return $table;
    }

    private function loadCatchTable(int $offset, int $size): ?Entries
    {
        if ($size <= 0) {
            return null;
        }
        $this->stream->move($offset);
        $entries = new Entries();

        for ($i = 0; $i < $size; $i++) {
            $iseqIndex = $this->readSmallValue();
            $entries[] = new CatchTableEntry(
                type: $this->readSmallValue(),
                start: $this->readSmallValue(),
                end: $this->readSmallValue(),
                cont: $this->readSmallValue(),
                sp: $this->readSmallValue(),
                iseq: $this->loadISeq($iseqIndex),
            );
        }

        return $entries;
    }

    private function loadISeq(int $index): ?ISeq
    {
        if ($index === -1) {
            return null;
        }

        if (isset($this->iseqList[$index])) {
            return $this->iseqList[$index];
        }

        $iseq = new ISeq(
            aux: new Aux(
                loader: new AuxLoader(
                    obj: 0, // TODO: Move loader into new class
                    index: $index,
                ),
            ),
            data: null,
        );

        $this->iseqList[$index] = $iseq;

        $iseq = $iseq->update(
            data: $this->loadISeqEach($iseq),
        );

        return $iseq;
    }

    private function loadCode(ObjectBody $objectBody, int $bytecodeOffset, int $bytecodeSize, int $iseqSize): array
    {
        $this->stream->move($bytecodeOffset);
        $typeCharMap = implode($this->operationCodes);
        $code = [];

        $cdEntryIndex = 0;

        for ($codeIndex = 0; $codeIndex < $iseqSize;) {
            $insn = $code[$codeIndex] = $this->readSmallValue();
            $types = $typeCharMap[$this->operationOffsets[$insn]];
            $codeIndex++;


            // NOTE: $types[$opIndex] get a string type but it is needing byte type
            for ($opIndex = 0; ord($types[$opIndex] ?? "\0"); $opIndex++, $codeIndex++) {
                $operandType = $types[$opIndex];
                switch ($operandType) {
                    case InsnType::TS_VALUE->value:
                        $op = $this->readSmallValue();
                        $v = $this->loadObject($op);
                        $code[$codeIndex] = $v;
                        break;
                    case InsnType::TS_CDHASH->value:
                        throw new RubyVMRaise('TS_CDHASH is not supported');
                        break;
                    case InsnType::TS_ISEQ->value:
                        throw new RubyVMRaise('TS_ISEQ is not supported');
                        break;
                    case InsnType::TS_ISE->value:
                    case InsnType::TS_ICVARC->value:
                    case InsnType::TS_IVC->value:
                        throw new RubyVMRaise('TS_FUNCPTR is not supported');
                        break;
                    case InsnType::TS_CALLDATA->value:
                        $code[$codeIndex] = $objectBody->callInfoEntries[$cdEntryIndex++];
                        break;
                    case InsnType::TS_ID->value:
                        throw new RubyVMRaise('TS_ID is not supported');
                        break;
                    case InsnType::TS_FUNCPTR->value:
                        throw new RubyVMRaise('TS_FUNCPTR is not supported');
                    break;
                    case InsnType::TS_BUILTIN->value:
                        throw new RubyVMRaise('TS_BUILTIN is not supported');
                        break;
                    default:
                        $code[$codeIndex] = $this->readSmallValue();
                        break;
                };
            }
        }

        return $code;
    }

    private function loadObjectUnsupported(int $offset, ObjectHeader $objectHeader): mixed
    {
        throw new RubyVMException("THe specified object is unsupported (offset: {$offset})");
    }

    private function readString(int $length, RubyEncodingIndex $encodingIndex): string
    {
        return $this->stream->read($length);
    }
}
