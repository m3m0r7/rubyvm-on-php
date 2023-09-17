<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

trait ProvidePHPClassMethods
{
    public function phpinfo(): void
    {
        ob_start();
        phpinfo(INFO_ALL);
        $info = ob_get_clean();
        $this->kernel->IOContext()->stdOut->write($info);
    }


}
