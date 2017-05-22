<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 22/05/2017
 * Time: 22:27
 */

namespace Mgi\CoreBundle\Migration;


interface MigratableInterface
{
    /**
     * Returns version string
     *
     * @return string
     */
    public function getVersion();

    /**
     * Returns the location of migration files
     *
     * @return string
     */
    public function getMigrationPath();

    /**
     * Returns the priority number
     *
     * @return int
     */
    public function getPriority();

    /**
     * Returns the bundles' name
     *
     * @return string
     */
    public function getName();
}