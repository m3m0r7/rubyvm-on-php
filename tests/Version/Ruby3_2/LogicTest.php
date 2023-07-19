<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\Runtime\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

class LogicTest extends TestApplication
{
    public function testForStatementWithLocalVariable()
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            test_array = [1, 2, 3, 4, 5]

            for i in test_array
              puts i.to_s + "\n"
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<_
        1
        2
        3
        4
        5

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testForStatementWithLocalVariableAnotherSyntax()
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            [1, 2, 3, 4, 5].each do | i |
              puts i.to_s + "\n"
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<_
        1
        2
        3
        4
        5

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testFizzBuzz()
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            for i in 1..100 do
              if i % 15 === 0
                puts "FizzBuzz\n"
              elsif i % 5 === 0
                puts "Buzz\n"
              elsif i % 3 === 0
                puts "Fizz\n"
              else
                puts i.to_s + "\n"
              end
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<_
        1
        2
        Fizz
        4
        Buzz
        Fizz
        7
        8
        Fizz
        Buzz
        11
        Fizz
        13
        14
        FizzBuzz
        16
        17
        Fizz
        19
        Buzz
        Fizz
        22
        23
        Fizz
        Buzz
        26
        Fizz
        28
        29
        FizzBuzz
        31
        32
        Fizz
        34
        Buzz
        Fizz
        37
        38
        Fizz
        Buzz
        41
        Fizz
        43
        44
        FizzBuzz
        46
        47
        Fizz
        49
        Buzz
        Fizz
        52
        53
        Fizz
        Buzz
        56
        Fizz
        58
        59
        FizzBuzz
        61
        62
        Fizz
        64
        Buzz
        Fizz
        67
        68
        Fizz
        Buzz
        71
        Fizz
        73
        74
        FizzBuzz
        76
        77
        Fizz
        79
        Buzz
        Fizz
        82
        83
        Fizz
        Buzz
        86
        Fizz
        88
        89
        FizzBuzz
        91
        92
        Fizz
        94
        Buzz
        Fizz
        97
        98
        Fizz
        Buzz

        _, $rubyVMManager->stdOut->readAll());
    }


    public function testFibonacci()
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
        $this->assertSame(<<<_
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
