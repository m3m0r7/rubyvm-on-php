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
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("25\n", $rubyVMManager->stdOut->readAll());
    }

    /**
     * This test ensure environment indexes. The method is increasing an environment index of starting from default size
     * when using arrays in definemethod and defineclass insn.
     */
    public function testInternalEnvIndexTest(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def internal(arr1, arr2, arr3)
                arr4 = Array.new
                arr5 = Array.new
                arr6 = Array.new

                arr4.push 1000
                arr4.push 1010
                arr4.push 1100

                puts arr1 + arr2 + arr3 + arr4 + arr5 + arr6 + [arr4[0]] + [arr4[1]] + [arr4[2]]
            end

            puts internal([1, 10, 100], [10, 100, 1000], [1100, 1110, 1111])
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        1
        10
        100
        10
        100
        1000
        1100
        1110
        1111
        1000
        1010
        1100
        1000
        1010
        1100


        _, $rubyVMManager->stdOut->readAll());
    }
}
