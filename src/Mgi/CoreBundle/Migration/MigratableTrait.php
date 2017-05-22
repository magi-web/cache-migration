<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 29/05/2017
 * Time: 21:57
 */

namespace Mgi\CoreBundle\Migration;


trait MigratableTrait
{
    /**
     * Returns the location of migration files relative to the bundle path
     *
     * @return string
     */
    public function getMigrationPath()
    {
        return parent::getPath()
            . DIRECTORY_SEPARATOR . "Resources"
            . DIRECTORY_SEPARATOR . "migrations"
            . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the priority number
     *
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }
}