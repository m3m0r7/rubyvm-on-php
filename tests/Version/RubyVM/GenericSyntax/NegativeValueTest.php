<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\GenericSyntax;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class NegativeValueTest extends TestApplication
{
    public function testNegativeValue(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts -32
            puts -16
            puts -8
            puts -7
            puts -3
            puts -2
            puts -1
            puts 0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        -32
        -16
        -8
        -7
        -3
        -2
        -1
        0

        _, $rubyVMManager->stdOut->readAll());
    }
}
