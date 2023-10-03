<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\Method;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class PTest extends TestApplication
{
    public function testObjectIsNil(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            p "Hello World!"
            p [1, 2, 3, 4, 5, 6]
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        "Hello World!"
        [1, 2, 3, 4, 5, 6]

        _, $rubyVMManager->stdOut->readAll());
    }
}
