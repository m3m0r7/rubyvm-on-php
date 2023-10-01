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
class LocalVariableTest extends TestApplication
{
    public function testLocalVariable(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            variable_test1 = 15
            variable_test2 = 10
            puts variable_test1 + variable_test2
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("25\n", $rubyVMManager->stdOut->readAll());
    }
}
