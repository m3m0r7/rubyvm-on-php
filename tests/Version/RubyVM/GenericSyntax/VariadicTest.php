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
class VariadicTest extends TestApplication
{
    public function testVariadicArguments(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def variadic_arguments(a, b, *params)
              puts a
              params.each do |param|
                puts param
              end

              # Check if VM stack is not empty (call a method testing)
              puts b
            end
            variadic_arguments(1, 5, 2, 3, 4)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1\n2\n3\n4\n5\n", $rubyVMManager->stdOut->readAll());
    }
}
