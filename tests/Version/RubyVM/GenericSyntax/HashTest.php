<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\GenericSyntax;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class HashTest extends TestApplication
{
    public function testHash(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            obj = { a: :symbol, b: 1234, c: 'Hello World!' }

            puts obj[:a]
            puts obj[:b]
            puts obj[:c]

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        symbol
        1234
        Hello World!

        _, $rubyVMManager->stdOut->readAll());
    }
}
