<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn;

use RubyVM\VM\Core\Helper\EnumIntValueFindable;

enum Insn: int
{
    use EnumIntValueFindable;

    case NOP = 0;

    case GETLOCAL = 1;

    case SETLOCAL = 2;

    case GETBLOCKPARAM = 3;

    case SETBLOCKPARAM = 4;

    case GETBLOCKPARAMPROXY = 5;

    case GETSPECIAL = 6;

    case SETSPECIAL = 7;

    case GETINSTANCEVARIABLE = 8;

    case SETINSTANCEVARIABLE = 9;

    case GETCLASSVARIABLE = 10;

    case SETCLASSVARIABLE = 11;

    case OPT_GETCONSTANT_PATH = 12;

    case GETCONSTANT = 13;

    case SETCONSTANT = 14;

    case GETGLOBAL = 15;

    case SETGLOBAL = 16;

    case PUTNIL = 17;

    case PUTSELF = 18;

    case PUTOBJECT = 19;

    case PUTSPECIALOBJECT = 20;

    case PUTSTRING = 21;

    case CONCATSTRINGS = 22;

    case ANYTOSTRING = 23;

    case TOREGEXP = 24;

    case INTERN = 25;

    case NEWARRAY = 26;

    case NEWARRAYKWSPLAT = 27;

    case DUPARRAY = 28;

    case DUPHASH = 29;

    case EXPANDARRAY = 30;

    case CONCATARRAY = 31;

    case SPLATARRAY = 32;

    case NEWHASH = 33;

    case NEWRANGE = 34;

    case POP = 35;

    case DUP = 36;

    case DUPN = 37;

    case SWAP = 38;

    case OPT_REVERSE = 39;

    case TOPN = 40;

    case SETN = 41;

    case ADJUSTSTACK = 42;

    case DEFINED = 43;

    case CHECKMATCH = 44;

    case CHECKKEYWORD = 45;

    case CHECKTYPE = 46;

    case DEFINECLASS = 47;

    case DEFINEMETHOD = 48;

    case DEFINESMETHOD = 49;

    case SEND = 50;

    case OPT_SEND_WITHOUT_BLOCK = 51;

    case OBJTOSTRING = 52;

    case OPT_STR_FREEZE = 53;

    case OPT_NIL_P = 54;

    case OPT_STR_UMINUS = 55;

    case OPT_NEWARRAY_MAX = 56;

    case OPT_NEWARRAY_MIN = 57;

    case INVOKESUPER = 58;

    case INVOKEBLOCK = 59;

    case LEAVE = 60;

    case THROW = 61;

    case JUMP = 62;

    case BRANCHIF = 63;

    case BRANCHUNLESS = 64;

    case BRANCHNIL = 65;

    case ONCE = 66;

    case OPT_CASE_DISPATCH = 67;

    case OPT_PLUS = 68;

    case OPT_MINUS = 69;

    case OPT_MULT = 70;

    case OPT_DIV = 71;

    case OPT_MOD = 72;

    case OPT_EQ = 73;

    case OPT_NEQ = 74;

    case OPT_LT = 75;

    case OPT_LE = 76;

    case OPT_GT = 77;

    case OPT_GE = 78;

    case OPT_LTLT = 79;

    case OPT_AND = 80;

    case OPT_OR = 81;

    case OPT_AREF = 82;

    case OPT_ASET = 83;

    case OPT_ASET_WITH = 84;

    case OPT_AREF_WITH = 85;

    case OPT_LENGTH = 86;

    case OPT_SIZE = 87;

    case OPT_EMPTY_P = 88;

    case OPT_SUCC = 89;

    case OPT_NOT = 90;

    case OPT_REGEXPMATCH2 = 91;

    case INVOKEBUILTIN = 92;

    case OPT_INVOKEBUILTIN_DELEGATE = 93;

    case OPT_INVOKEBUILTIN_DELEGATE_LEAVE = 94;

    case GETLOCAL_WC_0 = 95;

    case GETLOCAL_WC_1 = 96;

    case SETLOCAL_WC_0 = 97;

    case SETLOCAL_WC_1 = 98;

    case PUTOBJECT_INT2FIX_0_ = 99;

    case PUTOBJECT_INT2FIX_1_ = 100;

    case TRACE_NOP = 101;

    case TRACE_GETLOCAL = 102;

    case TRACE_SETLOCAL = 103;

    case TRACE_GETBLOCKPARAM = 104;

    case TRACE_SETBLOCKPARAM = 105;

    case TRACE_GETBLOCKPARAMPROXY = 106;

    case TRACE_GETSPECIAL = 107;

    case TRACE_SETSPECIAL = 108;

    case TRACE_GETINSTANCEVARIABLE = 109;

    case TRACE_SETINSTANCEVARIABLE = 110;

    case TRACE_GETCLASSVARIABLE = 111;

    case TRACE_SETCLASSVARIABLE = 112;

    case TRACE_OPT_GETCONSTANT_PATH = 113;

    case TRACE_GETCONSTANT = 114;

    case TRACE_SETCONSTANT = 115;

    case TRACE_GETGLOBAL = 116;

    case TRACE_SETGLOBAL = 117;

    case TRACE_PUTNIL = 118;

    case TRACE_PUTSELF = 119;

    case TRACE_PUTOBJECT = 120;

    case TRACE_PUTSPECIALOBJECT = 121;

    case TRACE_PUTSTRING = 122;

    case TRACE_CONCATSTRINGS = 123;

    case TRACE_ANYTOSTRING = 124;

    case TRACE_TOREGEXP = 125;

    case TRACE_INTERN = 126;

    case TRACE_NEWARRAY = 127;

    case TRACE_NEWARRAYKWSPLAT = 128;

    case TRACE_DUPARRAY = 129;

    case TRACE_DUPHASH = 130;

    case TRACE_EXPANDARRAY = 131;

    case TRACE_CONCATARRAY = 132;

    case TRACE_SPLATARRAY = 133;

    case TRACE_NEWHASH = 134;

    case TRACE_NEWRANGE = 135;

    case TRACE_POP = 136;

    case TRACE_DUP = 137;

    case TRACE_DUPN = 138;

    case TRACE_SWAP = 139;

    case TRACE_OPT_REVERSE = 140;

    case TRACE_TOPN = 141;

    case TRACE_SETN = 142;

    case TRACE_ADJUSTSTACK = 143;

    case TRACE_DEFINED = 144;

    case TRACE_CHECKMATCH = 145;

    case TRACE_CHECKKEYWORD = 146;

    case TRACE_CHECKTYPE = 147;

    case TRACE_DEFINECLASS = 148;

    case TRACE_DEFINEMETHOD = 149;

    case TRACE_DEFINESMETHOD = 150;

    case TRACE_SEND = 151;

    case TRACE_OPT_SEND_WITHOUT_BLOCK = 152;

    case TRACE_OBJTOSTRING = 153;

    case TRACE_OPT_STR_FREEZE = 154;

    case TRACE_OPT_NIL_P = 155;

    case TRACE_OPT_STR_UMINUS = 156;

    case TRACE_OPT_NEWARRAY_MAX = 157;

    case TRACE_OPT_NEWARRAY_MIN = 158;

    case TRACE_INVOKESUPER = 159;

    case TRACE_INVOKEBLOCK = 160;

    case TRACE_LEAVE = 161;

    case TRACE_THROW = 162;

    case TRACE_JUMP = 163;

    case TRACE_BRANCHIF = 164;

    case TRACE_BRANCHUNLESS = 165;

    case TRACE_BRANCHNIL = 166;

    case TRACE_ONCE = 167;

    case TRACE_OPT_CASE_DISPATCH = 168;

    case TRACE_OPT_PLUS = 169;

    case TRACE_OPT_MINUS = 170;

    case TRACE_OPT_MULT = 171;

    case TRACE_OPT_DIV = 172;

    case TRACE_OPT_MOD = 173;

    case TRACE_OPT_EQ = 174;

    case TRACE_OPT_NEQ = 175;

    case TRACE_OPT_LT = 176;

    case TRACE_OPT_LE = 177;

    case TRACE_OPT_GT = 178;

    case TRACE_OPT_GE = 179;

    case TRACE_OPT_LTLT = 180;

    case TRACE_OPT_AND = 181;

    case TRACE_OPT_OR = 182;

    case TRACE_OPT_AREF = 183;

    case TRACE_OPT_ASET = 184;

    case TRACE_OPT_ASET_WITH = 185;

    case TRACE_OPT_AREF_WITH = 186;

    case TRACE_OPT_LENGTH = 187;

    case TRACE_OPT_SIZE = 188;

    case TRACE_OPT_EMPTY_P = 189;

    case TRACE_OPT_SUCC = 190;

    case TRACE_OPT_NOT = 191;

    case TRACE_OPT_REGEXPMATCH2 = 192;

    case TRACE_INVOKEBUILTIN = 193;

    case TRACE_OPT_INVOKEBUILTIN_DELEGATE = 194;

    case TRACE_OPT_INVOKEBUILTIN_DELEGATE_LEAVE = 195;

    case TRACE_GETLOCAL_WC_0 = 196;

    case TRACE_GETLOCAL_WC_1 = 197;

    case TRACE_SETLOCAL_WC_0 = 198;

    case TRACE_SETLOCAL_WC_1 = 199;

    case TRACE_PUTOBJECT_INT2FIX_0_ = 200;

    case TRACE_PUTOBJECT_INT2FIX_1_ = 201;

    public function operandSize(): int
    {
        // TODO: Mapping INSNs operand size
        return match ($this) {
            Insn::DEFINEMETHOD,
            Insn::SETINSTANCEVARIABLE,
            Insn::GETINSTANCEVARIABLE,
            Insn::SEND => 2,
            Insn::DEFINECLASS => 3,
            Insn::SETLOCAL, Insn::GETLOCAL => 2,
            Insn::OPT_CASE_DISPATCH, Insn::GETBLOCKPARAMPROXY => 2,
            default => 1,
        };
    }
}
