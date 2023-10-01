<?php

declare(strict_types=1);

namespace RubyVM\VM\Exception;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;

class Raise extends RubyVMException implements \Stringable
{
    public function __construct(protected ContextInterface $context, protected string $errorClass, string $message)
    {
        parent::__construct($message, 0);
    }

    public function __toString(): string
    {
        return sprintf(
            "%s:%d:in '%s': %s (\e[4m%s\e[0m)",
            $this->context->instructionSequence()->body()->info()->path(),
            // TODO: We will implements line no from an instruction sequence details
            -1,
            $this->context->modulePath(),
            $this->message,
            $this->errorClass,
        );
    }
}
