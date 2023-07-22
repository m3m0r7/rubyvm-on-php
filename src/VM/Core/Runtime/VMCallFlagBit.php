<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

enum VMCallFlagBit: int
{
    case VM_CALL_ARGS_SPLAT = 0;     // m(*args)

    case VM_CALL_ARGS_BLOCKARG = 1;  // m(&block)

    case VM_CALL_FCALL = 2;          // m(...)

    case VM_CALL_VCALL = 3;          // m

    case VM_CALL_ARGS_SIMPLE = 4;    // (ci->flag & (SPLAT|BLOCKARG)) && blockiseq == NULL && ci->kw_arg == NULL

    case VM_CALL_BLOCKISEQ = 5;      // has blockiseq

    case VM_CALL_KWARG = 6;          // has kwarg

    case VM_CALL_KW_SPLAT = 7;       // m(**opts)

    case VM_CALL_TAILCALL = 8;       // located at tail position

    case VM_CALL_SUPER = 9;          // super

    case VM_CALL_ZSUPER = 10;        // zsuper

    case VM_CALL_OPT_SEND = 11;      // internal flag

    case VM_CALL_KW_SPLAT_MUT = 12;  // kw splat hash can be modified (to avoid allocating a new one)
};
