<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\GenericSyntax;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class GlobalVariableTest extends TestApplication
{
    public function testGlobalVariable(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            $globalVar = 'Hello World!'
            puts $globalVar

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        Hello World!

        _, $rubyVMManager->stdOut->readAll());
    }
}
