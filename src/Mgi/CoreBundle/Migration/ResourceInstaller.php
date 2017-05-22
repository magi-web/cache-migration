<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 16/06/2017
 * Time: 00:01
 */

namespace Mgi\CoreBundle\Migration;


use Doctrine\ORM\EntityManager;

/**
 * Class ResourceInstaller
 * @package Mgi\CoreBundle\Migration
 */
class ResourceInstaller
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * ResourceInstaller constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Gets the namespace.
     *
     * @return string The namespace
     */
    public function getNamespace()
    {
        $class = get_class($this);

        return substr($class, 0, strrpos($class, '\\'));
    }

    /**
     * @param $resourceFile
     */
    public function importResourceFile($resourceFile)
    {
        include $resourceFile;
    }
}