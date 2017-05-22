<?php

namespace AppBundle;

use Mgi\CoreBundle\Migration\MigratableInterface;
use Mgi\CoreBundle\Migration\MigratableTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle implements MigratableInterface
{
    use MigratableTrait;

    public function getVersion()
    {
        return "";
    }
}
