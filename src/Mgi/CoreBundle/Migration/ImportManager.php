<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 16/06/2017
 * Time: 00:39
 */

namespace Mgi\CoreBundle\Migration;


use Doctrine\ORM\EntityManager;
use Mgi\CoreBundle\Entity\Resource;
use Mgi\CoreBundle\Util\MigrationUtil;
use Monolog\Logger;

class ImportManager
{
    private $em;
    private $logger;
    private $manager;

    public function __construct(EntityManager $em, Logger $logger, Manager $manager)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->manager = $manager;
    }

    /**
     *
     */
    public function doMigrations()
    {
        try {
            $bundles = $this->manager->getMigratableBundles();
            /**
             * @var string $key
             * @var MigratableInterface $bundle
             */
            foreach ($bundles as $key => $bundle) {
                $this->doMigrateBundle($bundle);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        } finally {
            $this->em->flush();
        }

    }


    /**
     * @param MigratableInterface $bundle
     */
    private function doMigrateBundle(MigratableInterface $bundle)
    {
        $resourcesVersions = $this->manager->getResourcesVersion();

        /** @var \Mgi\CoreBundle\Entity\Resource $resourceModel */
        $resourceModel = array_key_exists($bundle->getName(), $resourcesVersions)
            ? $resourcesVersions[$bundle->getName()]
            : new Resource();

        $resourceModel->setResourceName($bundle->getName());

        $graph = MigrationUtil::buildMigrationGraph($bundle->getMigrationPath());

        $currentFromVersion = !empty($resourceModel->getId()) ? $resourceModel->getResourceVersion() : 0;
        while (array_key_exists($currentFromVersion, $graph)) {
            list($targetVersion, $migrationFile) = $graph[$currentFromVersion];

            $this->manager->getResourceInstaller()->importResourceFile($migrationFile);
            $currentFromVersion = $targetVersion;
        }

        $resourceModel->setResourceVersion($currentFromVersion);

        $this->em->persist($resourceModel);

        //Note : $this->em->flush is done in the finally portion of doMigrations method
    }
}