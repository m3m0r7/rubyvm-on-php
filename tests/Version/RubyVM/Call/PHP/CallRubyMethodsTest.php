<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\PHP;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class CallRubyMethodsTest extends TestApplication
{
    public function testCallUserlandMethodWithoutArgumentsFromPHP(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def test()
              65535
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $executed = $executor->execute();
        $this->assertSame(ExecutedStatus::SUCCESS, $executed->executedStatus);
        $this->assertSame(
            65535,
            // @phpstan-ignore-next-line
            $executed->context()->test(),
        );
    }

    public function testCallUserlandMethodFromPHP(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def quicksort(arr)
              return arr if arr.size == 0
              pivot = arr[0]
              right = Array.new
              left = Array.new
              for i in 1..arr.size-1
                if arr[i] <= pivot
                  left.push(arr[i])
                else
                  right.push(arr[i])
                end
              end

              left = quicksort(left)
              right = quicksort(right)
              left + [pivot] + right
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $executed = $executor->execute();
        $this->assertSame(ExecutedStatus::SUCCESS, $executed->executedStatus);
        $this->assertSame(
            [
                -32,
                -8,
                1,
                64,
                256,
                512,
                1024,
                2048,
            ],
            // @phpstan-ignore-next-line
            $executed->context()->quicksort([-8, 1, 2048, 512, 64, 256, 1024, -32]),
        );
    }
}
