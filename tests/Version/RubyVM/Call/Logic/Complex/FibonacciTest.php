<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\Call\Complex;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class FibonacciTest extends TestApplication
{
    public function testFibonacci(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            def fib(i)
              if i == 0
                return 0
              end
              if i == 1
                return 1
              end
              fib(i - 1) + fib(i - 2)
            end

            for i in 1..10 do
              puts fib(i).to_s + "\n"
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        1
        1
        2
        3
        5
        8
        13
        21
        34
        55

        _, $rubyVMManager->stdOut->readAll());
    }
}
