<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Executor\OperationProcessorEntries;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinAdjuststack;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinAnytostring;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinBranchif;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinBranchnil;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinBranchunless;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinCheckkeyword;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinCheckmatch;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinChecktype;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinConcatarray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinConcatstrings;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinDefineclass;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinDefined;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinDefinemethod;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinDefinesmethod;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinDup;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinDuparray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinDuphash;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinDupn;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinExpandarray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetblockparam;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetblockparamproxy;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetclassvariable;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetconstant;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetglobal;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetinstancevariable;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetlocal;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetlocalWC0;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetlocalWC1;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinGetspecial;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinIntern;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinInvokeblock;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinInvokebuiltin;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinInvokesuper;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinJump;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinLeave;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinNewarray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinNewarraykwsplat;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinNewhash;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinNewrange;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinNop;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinObjtostring;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOnce;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptAnd;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptAref;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptArefWith;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptAset;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptAsetWith;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptCaseDispatch;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptDiv;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptEmptyP;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptEq;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptGe;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptGetconstantPath;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptGt;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptInvokebuiltinDelegate;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptInvokebuiltinDelegateLeave;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptLe;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptLength;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptLt;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptLtlt;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptMinus;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptMod;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptMult;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptNeq;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptNewarrayMax;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptNewarrayMin;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptNilP;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptNot;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptOr;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptPlus;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptRegexpmatch2;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptReverse;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptSendWithoutBlock;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptSize;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptStrFreeze;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptStrUminus;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinOptSucc;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinPop;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinPutnil;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinPutobject;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinPutobjectINT2FIX0;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinPutobjectINT2FIX1;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinPutself;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinPutspecialobject;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinPutstring;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSend;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetblockparam;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetclassvariable;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetconstant;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetglobal;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetinstancevariable;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetlocal;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetlocalWC0;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetlocalWC1;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetn;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSetspecial;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSplatarray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinSwap;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinThrow;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTopn;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinToregexp;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceAdjuststack;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceAnytostring;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceBranchif;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceBranchnil;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceBranchunless;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceCheckkeyword;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceCheckmatch;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceChecktype;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceConcatarray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceConcatstrings;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceDefineclass;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceDefined;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceDefinemethod;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceDefinesmethod;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceDup;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceDuparray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceDuphash;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceDupn;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceExpandarray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetblockparam;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetblockparamproxy;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetclassvariable;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetconstant;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetglobal;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetinstancevariable;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetlocal;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetlocalWC0;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetlocalWC1;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceGetspecial;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceIntern;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceInvokeblock;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceInvokebuiltin;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceInvokesuper;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceJump;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceLeave;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceNewarray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceNewarraykwsplat;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceNewhash;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceNewrange;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceNop;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceObjtostring;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOnce;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptAnd;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptAref;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptArefWith;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptAset;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptAsetWith;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptCaseDispatch;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptDiv;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptEmptyP;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptEq;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptGe;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptGetconstantPath;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptGt;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptInvokebuiltinDelegate;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptInvokebuiltinDelegateLeave;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptLe;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptLength;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptLt;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptLtlt;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptMinus;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptMod;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptMult;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptNeq;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptNewarrayMax;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptNewarrayMin;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptNilP;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptNot;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptOr;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptPlus;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptRegexpmatch2;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptReverse;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptSendWithoutBlock;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptSize;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptStrFreeze;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptStrUminus;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceOptSucc;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTracePop;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTracePutnil;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTracePutobject;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTracePutobjectINT2FIX0;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTracePutobjectINT2FIX1;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTracePutself;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTracePutspecialobject;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTracePutstring;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSend;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetblockparam;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetclassvariable;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetconstant;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetglobal;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetinstancevariable;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetlocal;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetlocalWC0;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetlocalWC1;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetn;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSetspecial;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSplatarray;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceSwap;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceThrow;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceTopn;
use RubyVM\VM\Core\Runtime\Insn\Processor\BuiltinTraceToregexp;

final class DefaultOperationProcessorEntries extends OperationProcessorEntries
{
    public function __construct(array $items = [])
    {
        parent::__construct($items);
        $this->set(Insn::NOP, new BuiltinNop());
        $this->set(Insn::GETLOCAL, new BuiltinGetlocal());
        $this->set(Insn::SETLOCAL, new BuiltinSetlocal());
        $this->set(Insn::GETBLOCKPARAM, new BuiltinGetblockparam());
        $this->set(Insn::SETBLOCKPARAM, new BuiltinSetblockparam());
        $this->set(Insn::GETBLOCKPARAMPROXY, new BuiltinGetblockparamproxy());
        $this->set(Insn::GETSPECIAL, new BuiltinGetspecial());
        $this->set(Insn::SETSPECIAL, new BuiltinSetspecial());
        $this->set(Insn::GETINSTANCEVARIABLE, new BuiltinGetinstancevariable());
        $this->set(Insn::SETINSTANCEVARIABLE, new BuiltinSetinstancevariable());
        $this->set(Insn::GETCLASSVARIABLE, new BuiltinGetclassvariable());
        $this->set(Insn::SETCLASSVARIABLE, new BuiltinSetclassvariable());
        $this->set(Insn::OPT_GETCONSTANT_PATH, new BuiltinOptGetconstantPath());
        $this->set(Insn::GETCONSTANT, new BuiltinGetconstant());
        $this->set(Insn::SETCONSTANT, new BuiltinSetconstant());
        $this->set(Insn::GETGLOBAL, new BuiltinGetglobal());
        $this->set(Insn::SETGLOBAL, new BuiltinSetglobal());
        $this->set(Insn::PUTNIL, new BuiltinPutnil());
        $this->set(Insn::PUTSELF, new BuiltinPutself());
        $this->set(Insn::PUTOBJECT, new BuiltinPutobject());
        $this->set(Insn::PUTSPECIALOBJECT, new BuiltinPutspecialobject());
        $this->set(Insn::PUTSTRING, new BuiltinPutstring());
        $this->set(Insn::CONCATSTRINGS, new BuiltinConcatstrings());
        $this->set(Insn::ANYTOSTRING, new BuiltinAnytostring());
        $this->set(Insn::TOREGEXP, new BuiltinToregexp());
        $this->set(Insn::INTERN, new BuiltinIntern());
        $this->set(Insn::NEWARRAY, new BuiltinNewarray());
        $this->set(Insn::NEWARRAYKWSPLAT, new BuiltinNewarraykwsplat());
        $this->set(Insn::DUPARRAY, new BuiltinDuparray());
        $this->set(Insn::DUPHASH, new BuiltinDuphash());
        $this->set(Insn::EXPANDARRAY, new BuiltinExpandarray());
        $this->set(Insn::CONCATARRAY, new BuiltinConcatarray());
        $this->set(Insn::SPLATARRAY, new BuiltinSplatarray());
        $this->set(Insn::NEWHASH, new BuiltinNewhash());
        $this->set(Insn::NEWRANGE, new BuiltinNewrange());
        $this->set(Insn::POP, new BuiltinPop());
        $this->set(Insn::DUP, new BuiltinDup());
        $this->set(Insn::DUPN, new BuiltinDupn());
        $this->set(Insn::SWAP, new BuiltinSwap());
        $this->set(Insn::OPT_REVERSE, new BuiltinOptReverse());
        $this->set(Insn::TOPN, new BuiltinTopn());
        $this->set(Insn::SETN, new BuiltinSetn());
        $this->set(Insn::ADJUSTSTACK, new BuiltinAdjuststack());
        $this->set(Insn::DEFINED, new BuiltinDefined());
        $this->set(Insn::CHECKMATCH, new BuiltinCheckmatch());
        $this->set(Insn::CHECKKEYWORD, new BuiltinCheckkeyword());
        $this->set(Insn::CHECKTYPE, new BuiltinChecktype());
        $this->set(Insn::DEFINECLASS, new BuiltinDefineclass());
        $this->set(Insn::DEFINEMETHOD, new BuiltinDefinemethod());
        $this->set(Insn::DEFINESMETHOD, new BuiltinDefinesmethod());
        $this->set(Insn::SEND, new BuiltinSend());
        $this->set(Insn::OPT_SEND_WITHOUT_BLOCK, new BuiltinOptSendWithoutBlock());
        $this->set(Insn::OBJTOSTRING, new BuiltinObjtostring());
        $this->set(Insn::OPT_STR_FREEZE, new BuiltinOptStrFreeze());
        $this->set(Insn::OPT_NIL_P, new BuiltinOptNilP());
        $this->set(Insn::OPT_STR_UMINUS, new BuiltinOptStrUminus());
        $this->set(Insn::OPT_NEWARRAY_MAX, new BuiltinOptNewarrayMax());
        $this->set(Insn::OPT_NEWARRAY_MIN, new BuiltinOptNewarrayMin());
        $this->set(Insn::INVOKESUPER, new BuiltinInvokesuper());
        $this->set(Insn::INVOKEBLOCK, new BuiltinInvokeblock());
        $this->set(Insn::LEAVE, new BuiltinLeave());
        $this->set(Insn::THROW, new BuiltinThrow());
        $this->set(Insn::JUMP, new BuiltinJump());
        $this->set(Insn::BRANCHIF, new BuiltinBranchif());
        $this->set(Insn::BRANCHUNLESS, new BuiltinBranchunless());
        $this->set(Insn::BRANCHNIL, new BuiltinBranchnil());
        $this->set(Insn::ONCE, new BuiltinOnce());
        $this->set(Insn::OPT_CASE_DISPATCH, new BuiltinOptCaseDispatch());
        $this->set(Insn::OPT_PLUS, new BuiltinOptPlus());
        $this->set(Insn::OPT_MINUS, new BuiltinOptMinus());
        $this->set(Insn::OPT_MULT, new BuiltinOptMult());
        $this->set(Insn::OPT_DIV, new BuiltinOptDiv());
        $this->set(Insn::OPT_MOD, new BuiltinOptMod());
        $this->set(Insn::OPT_EQ, new BuiltinOptEq());
        $this->set(Insn::OPT_NEQ, new BuiltinOptNeq());
        $this->set(Insn::OPT_LT, new BuiltinOptLt());
        $this->set(Insn::OPT_LE, new BuiltinOptLe());
        $this->set(Insn::OPT_GT, new BuiltinOptGt());
        $this->set(Insn::OPT_GE, new BuiltinOptGe());
        $this->set(Insn::OPT_LTLT, new BuiltinOptLtlt());
        $this->set(Insn::OPT_AND, new BuiltinOptAnd());
        $this->set(Insn::OPT_OR, new BuiltinOptOr());
        $this->set(Insn::OPT_AREF, new BuiltinOptAref());
        $this->set(Insn::OPT_ASET, new BuiltinOptAset());
        $this->set(Insn::OPT_ASET_WITH, new BuiltinOptAsetWith());
        $this->set(Insn::OPT_AREF_WITH, new BuiltinOptArefWith());
        $this->set(Insn::OPT_LENGTH, new BuiltinOptLength());
        $this->set(Insn::OPT_SIZE, new BuiltinOptSize());
        $this->set(Insn::OPT_EMPTY_P, new BuiltinOptEmptyP());
        $this->set(Insn::OPT_SUCC, new BuiltinOptSucc());
        $this->set(Insn::OPT_NOT, new BuiltinOptNot());
        $this->set(Insn::OPT_REGEXPMATCH2, new BuiltinOptRegexpmatch2());
        $this->set(Insn::INVOKEBUILTIN, new BuiltinInvokebuiltin());
        $this->set(Insn::OPT_INVOKEBUILTIN_DELEGATE, new BuiltinOptInvokebuiltinDelegate());
        $this->set(Insn::OPT_INVOKEBUILTIN_DELEGATE_LEAVE, new BuiltinOptInvokebuiltinDelegateLeave());
        $this->set(Insn::GETLOCAL_WC_0, new BuiltinGetlocalWC0());
        $this->set(Insn::GETLOCAL_WC_1, new BuiltinGetlocalWC1());
        $this->set(Insn::SETLOCAL_WC_0, new BuiltinSetlocalWC0());
        $this->set(Insn::SETLOCAL_WC_1, new BuiltinSetlocalWC1());
        $this->set(Insn::PUTOBJECT_INT2FIX_0_, new BuiltinPutobjectINT2FIX0());
        $this->set(Insn::PUTOBJECT_INT2FIX_1_, new BuiltinPutobjectINT2FIX1());
        $this->set(Insn::TRACE_NOP, new BuiltinTraceNop());
        $this->set(Insn::TRACE_GETLOCAL, new BuiltinTraceGetlocal());
        $this->set(Insn::TRACE_SETLOCAL, new BuiltinTraceSetlocal());
        $this->set(Insn::TRACE_GETBLOCKPARAM, new BuiltinTraceGetblockparam());
        $this->set(Insn::TRACE_SETBLOCKPARAM, new BuiltinTraceSetblockparam());
        $this->set(Insn::TRACE_GETBLOCKPARAMPROXY, new BuiltinTraceGetblockparamproxy());
        $this->set(Insn::TRACE_GETSPECIAL, new BuiltinTraceGetspecial());
        $this->set(Insn::TRACE_SETSPECIAL, new BuiltinTraceSetspecial());
        $this->set(Insn::TRACE_GETINSTANCEVARIABLE, new BuiltinTraceGetinstancevariable());
        $this->set(Insn::TRACE_SETINSTANCEVARIABLE, new BuiltinTraceSetinstancevariable());
        $this->set(Insn::TRACE_GETCLASSVARIABLE, new BuiltinTraceGetclassvariable());
        $this->set(Insn::TRACE_SETCLASSVARIABLE, new BuiltinTraceSetclassvariable());
        $this->set(Insn::TRACE_OPT_GETCONSTANT_PATH, new BuiltinTraceOptGetconstantPath());
        $this->set(Insn::TRACE_GETCONSTANT, new BuiltinTraceGetconstant());
        $this->set(Insn::TRACE_SETCONSTANT, new BuiltinTraceSetconstant());
        $this->set(Insn::TRACE_GETGLOBAL, new BuiltinTraceGetglobal());
        $this->set(Insn::TRACE_SETGLOBAL, new BuiltinTraceSetglobal());
        $this->set(Insn::TRACE_PUTNIL, new BuiltinTracePutnil());
        $this->set(Insn::TRACE_PUTSELF, new BuiltinTracePutself());
        $this->set(Insn::TRACE_PUTOBJECT, new BuiltinTracePutobject());
        $this->set(Insn::TRACE_PUTSPECIALOBJECT, new BuiltinTracePutspecialobject());
        $this->set(Insn::TRACE_PUTSTRING, new BuiltinTracePutstring());
        $this->set(Insn::TRACE_CONCATSTRINGS, new BuiltinTraceConcatstrings());
        $this->set(Insn::TRACE_ANYTOSTRING, new BuiltinTraceAnytostring());
        $this->set(Insn::TRACE_TOREGEXP, new BuiltinTraceToregexp());
        $this->set(Insn::TRACE_INTERN, new BuiltinTraceIntern());
        $this->set(Insn::TRACE_NEWARRAY, new BuiltinTraceNewarray());
        $this->set(Insn::TRACE_NEWARRAYKWSPLAT, new BuiltinTraceNewarraykwsplat());
        $this->set(Insn::TRACE_DUPARRAY, new BuiltinTraceDuparray());
        $this->set(Insn::TRACE_DUPHASH, new BuiltinTraceDuphash());
        $this->set(Insn::TRACE_EXPANDARRAY, new BuiltinTraceExpandarray());
        $this->set(Insn::TRACE_CONCATARRAY, new BuiltinTraceConcatarray());
        $this->set(Insn::TRACE_SPLATARRAY, new BuiltinTraceSplatarray());
        $this->set(Insn::TRACE_NEWHASH, new BuiltinTraceNewhash());
        $this->set(Insn::TRACE_NEWRANGE, new BuiltinTraceNewrange());
        $this->set(Insn::TRACE_POP, new BuiltinTracePop());
        $this->set(Insn::TRACE_DUP, new BuiltinTraceDup());
        $this->set(Insn::TRACE_DUPN, new BuiltinTraceDupn());
        $this->set(Insn::TRACE_SWAP, new BuiltinTraceSwap());
        $this->set(Insn::TRACE_OPT_REVERSE, new BuiltinTraceOptReverse());
        $this->set(Insn::TRACE_TOPN, new BuiltinTraceTopn());
        $this->set(Insn::TRACE_SETN, new BuiltinTraceSetn());
        $this->set(Insn::TRACE_ADJUSTSTACK, new BuiltinTraceAdjuststack());
        $this->set(Insn::TRACE_DEFINED, new BuiltinTraceDefined());
        $this->set(Insn::TRACE_CHECKMATCH, new BuiltinTraceCheckmatch());
        $this->set(Insn::TRACE_CHECKKEYWORD, new BuiltinTraceCheckkeyword());
        $this->set(Insn::TRACE_CHECKTYPE, new BuiltinTraceChecktype());
        $this->set(Insn::TRACE_DEFINECLASS, new BuiltinTraceDefineclass());
        $this->set(Insn::TRACE_DEFINEMETHOD, new BuiltinTraceDefinemethod());
        $this->set(Insn::TRACE_DEFINESMETHOD, new BuiltinTraceDefinesmethod());
        $this->set(Insn::TRACE_SEND, new BuiltinTraceSend());
        $this->set(Insn::TRACE_OPT_SEND_WITHOUT_BLOCK, new BuiltinTraceOptSendWithoutBlock());
        $this->set(Insn::TRACE_OBJTOSTRING, new BuiltinTraceObjtostring());
        $this->set(Insn::TRACE_OPT_STR_FREEZE, new BuiltinTraceOptStrFreeze());
        $this->set(Insn::TRACE_OPT_NIL_P, new BuiltinTraceOptNilP());
        $this->set(Insn::TRACE_OPT_STR_UMINUS, new BuiltinTraceOptStrUminus());
        $this->set(Insn::TRACE_OPT_NEWARRAY_MAX, new BuiltinTraceOptNewarrayMax());
        $this->set(Insn::TRACE_OPT_NEWARRAY_MIN, new BuiltinTraceOptNewarrayMin());
        $this->set(Insn::TRACE_INVOKESUPER, new BuiltinTraceInvokesuper());
        $this->set(Insn::TRACE_INVOKEBLOCK, new BuiltinTraceInvokeblock());
        $this->set(Insn::TRACE_LEAVE, new BuiltinTraceLeave());
        $this->set(Insn::TRACE_THROW, new BuiltinTraceThrow());
        $this->set(Insn::TRACE_JUMP, new BuiltinTraceJump());
        $this->set(Insn::TRACE_BRANCHIF, new BuiltinTraceBranchif());
        $this->set(Insn::TRACE_BRANCHUNLESS, new BuiltinTraceBranchunless());
        $this->set(Insn::TRACE_BRANCHNIL, new BuiltinTraceBranchnil());
        $this->set(Insn::TRACE_ONCE, new BuiltinTraceOnce());
        $this->set(Insn::TRACE_OPT_CASE_DISPATCH, new BuiltinTraceOptCaseDispatch());
        $this->set(Insn::TRACE_OPT_PLUS, new BuiltinTraceOptPlus());
        $this->set(Insn::TRACE_OPT_MINUS, new BuiltinTraceOptMinus());
        $this->set(Insn::TRACE_OPT_MULT, new BuiltinTraceOptMult());
        $this->set(Insn::TRACE_OPT_DIV, new BuiltinTraceOptDiv());
        $this->set(Insn::TRACE_OPT_MOD, new BuiltinTraceOptMod());
        $this->set(Insn::TRACE_OPT_EQ, new BuiltinTraceOptEq());
        $this->set(Insn::TRACE_OPT_NEQ, new BuiltinTraceOptNeq());
        $this->set(Insn::TRACE_OPT_LT, new BuiltinTraceOptLt());
        $this->set(Insn::TRACE_OPT_LE, new BuiltinTraceOptLe());
        $this->set(Insn::TRACE_OPT_GT, new BuiltinTraceOptGt());
        $this->set(Insn::TRACE_OPT_GE, new BuiltinTraceOptGe());
        $this->set(Insn::TRACE_OPT_LTLT, new BuiltinTraceOptLtlt());
        $this->set(Insn::TRACE_OPT_AND, new BuiltinTraceOptAnd());
        $this->set(Insn::TRACE_OPT_OR, new BuiltinTraceOptOr());
        $this->set(Insn::TRACE_OPT_AREF, new BuiltinTraceOptAref());
        $this->set(Insn::TRACE_OPT_ASET, new BuiltinTraceOptAset());
        $this->set(Insn::TRACE_OPT_ASET_WITH, new BuiltinTraceOptAsetWith());
        $this->set(Insn::TRACE_OPT_AREF_WITH, new BuiltinTraceOptArefWith());
        $this->set(Insn::TRACE_OPT_LENGTH, new BuiltinTraceOptLength());
        $this->set(Insn::TRACE_OPT_SIZE, new BuiltinTraceOptSize());
        $this->set(Insn::TRACE_OPT_EMPTY_P, new BuiltinTraceOptEmptyP());
        $this->set(Insn::TRACE_OPT_SUCC, new BuiltinTraceOptSucc());
        $this->set(Insn::TRACE_OPT_NOT, new BuiltinTraceOptNot());
        $this->set(Insn::TRACE_OPT_REGEXPMATCH2, new BuiltinTraceOptRegexpmatch2());
        $this->set(Insn::TRACE_INVOKEBUILTIN, new BuiltinTraceInvokebuiltin());
        $this->set(Insn::TRACE_OPT_INVOKEBUILTIN_DELEGATE, new BuiltinTraceOptInvokebuiltinDelegate());
        $this->set(Insn::TRACE_OPT_INVOKEBUILTIN_DELEGATE_LEAVE, new BuiltinTraceOptInvokebuiltinDelegateLeave());
        $this->set(Insn::TRACE_GETLOCAL_WC_0, new BuiltinTraceGetlocalWC0());
        $this->set(Insn::TRACE_GETLOCAL_WC_1, new BuiltinTraceGetlocalWC1());
        $this->set(Insn::TRACE_SETLOCAL_WC_0, new BuiltinTraceSetlocalWC0());
        $this->set(Insn::TRACE_SETLOCAL_WC_1, new BuiltinTraceSetlocalWC1());
        $this->set(Insn::TRACE_PUTOBJECT_INT2FIX_0_, new BuiltinTracePutobjectINT2FIX0());
        $this->set(Insn::TRACE_PUTOBJECT_INT2FIX_1_, new BuiltinTracePutobjectINT2FIX1());
    }
}
