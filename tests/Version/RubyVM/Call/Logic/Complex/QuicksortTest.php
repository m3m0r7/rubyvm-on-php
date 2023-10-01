<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\Complex;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class QuicksortTest extends TestApplication
{
    public function testQuickSort(): void
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

            puts quicksort([-8, 1, 2048, 512, 64, 256, 1024, -32])
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        -32
        -8
        1
        64
        256
        512
        1024
        2048

        _, $rubyVMManager->stdOut->readAll());
    }
}
