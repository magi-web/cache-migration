<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 01/06/2017
 * Time: 22:34
 */

namespace Mgi\CoreBundle\Migration;

use Doctrine\ORM\EntityManager;
use Mgi\CoreBundle\Util\MigrationUtil;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class Manager
{
    const CORE_BUNDLE_NAME = 'MgiCoreBundle';
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ResourceInstaller
     */
    private $resourceInstaller;

    /**
     * @var Bundle[]
     */
    private $bundles;

    /** @var array */
    private $resourcesVersions;

    /**
     * Manager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, Logger $logger, ResourceInstaller $resourceInstaller)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->resourceInstaller = $resourceInstaller;

        $this->resourcesVersions = [];
        $this->bundles = [];
    }

    /**
     * Initializes the migratable bundles collection from the kernel
     *
     * @param \AppKernel $kernel
     */
    public function initBundles($kernel)
    {
        $this->setBundles($kernel->getBundles());
    }

    /**
     * @param Bundle[] $bundles
     * @return $this
     */
    public function setBundles(array $bundles)
    {
        //Make sure our core bundle has the highest priority
        $this->bundles = [self::CORE_BUNDLE_NAME => $bundles[self::CORE_BUNDLE_NAME]];

        // TODO handle priority
        foreach ($bundles as $bundle) {
            if ($bundle instanceof MigratableInterface) {
                $this->bundles[$bundle->getName()] = $bundle;
            }
        }

        return $this;
    }

    /**
     * @return Bundle[]
     */
    public function getMigratableBundles()
    {
        return $this->bundles;
    }

    /**
     * Tests if there are migrations files to run for the migratable bundles
     *
     * @return bool
     */
    public function isUpToDate() {
        $upToDate = true;
        $resourcesVersions = $this->getResourcesVersion();

        foreach ($this->bundles as $bundle) {
            $graph = MigrationUtil::buildMigrationGraph($bundle->getMigrationPath());
            if(array_key_exists($bundle->getName(), $resourcesVersions)) {
                $currentFromVersion = $resourcesVersions[$bundle->getName()]->getResourceVersion();
            } else {
                $currentFromVersion = 0;
            }

            if(array_key_exists($currentFromVersion, $graph)) {
                $upToDate = false;
                break;
            }
        }

        return $upToDate;
    }

    public function getResourcesVersion()
    {
        if (empty($this->resourcesVersions)) {
            // Test if resource table exists and fetch all entries
            $tableName = $this->em->getClassMetadata('MgiCoreBundle:Resource')->getTableName();
            $schemaManager = $this->em->getConnection()->getSchemaManager();
            if ($schemaManager->tablesExist(array($tableName))) {
                $resources = $this->em->getRepository('MgiCoreBundle:Resource')->findAll();
                /** @var \Mgi\CoreBundle\Entity\Resource $resource */
                foreach ($resources as $resource) {
                    $this->resourcesVersions[$resource->getResourceName()] = $resource;
                }
            }
        }

        return $this->resourcesVersions;
    }

    /**
     * @return ResourceInstaller
     */
    public function getResourceInstaller()
    {
        return $this->resourceInstaller;
    }

    public function getInstallerNamespace()
    {
        return $this->resourceInstaller->getNamespace();
    }

}