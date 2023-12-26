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
class KeywordArgumentsTest extends TestApplication
{
    public function testKeywordArguments(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def name(a:, b:, c:)
              puts a
              puts b
              puts c
            end
            name(c: "neko", a: "tanuki", b: "inu")

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("tanuki\ninu\nneko\n", $rubyVMManager->stdOut->readAll());
    }
}
