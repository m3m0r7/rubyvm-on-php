<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

trait ProvidePHPClassMethods
{
    public function phpinfo(): void
    {
        $this->kernel->IOContext()->stdOut->write('PHP Version: ' . PHP_VERSION . "\n");
    }
}
