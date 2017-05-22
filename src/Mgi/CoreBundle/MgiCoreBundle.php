<?php

namespace Mgi\CoreBundle;

use Mgi\CoreBundle\Migration\MigratableInterface;
use Mgi\CoreBundle\Migration\MigratableTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MgiCoreBundle extends Bundle implements MigratableInterface
{
    use MigratableTrait;

    public function getVersion()
    {
        return "1.0.0";
    }
}
