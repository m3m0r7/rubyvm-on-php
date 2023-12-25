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
class SymbolTest extends TestApplication
{
    public function testSymbol(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def symbol_test(a)
              puts a
            end

            symbol_test(:HelloWorld)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        HelloWorld

        _, $rubyVMManager->stdOut->readAll());
    }
}
