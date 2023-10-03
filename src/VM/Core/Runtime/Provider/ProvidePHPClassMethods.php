<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Exception\RuntimeException;

trait ProvidePHPClassMethods
{
    public function phpinfo(): NilClass
    {
        ob_start();
        phpinfo(INFO_ALL);
        $info = ob_get_clean();
        if ($info === false) {
            throw new RuntimeException('The buffer is invalid');
        }

        $this->context()->IOContext()->stdOut->write($info);

        return NilClass::createBy();
    }
}
