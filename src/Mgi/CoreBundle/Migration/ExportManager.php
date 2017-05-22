<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 16/06/2017
 * Time: 00:48
 */

namespace Mgi\CoreBundle\Migration;

use Doctrine\DBAL\Migrations\Provider\OrmSchemaProvider;
use Doctrine\ORM\EntityManager;
use Mgi\CoreBundle\Util\MigrationUtil;
use Monolog\Logger;


class ExportManager
{
    private static $_bundle_version_template = 'public function getVersion()
    {
        return "<version>";
    }';

    private static $_migration_template = '<?php

/** @var \<manager_namespace> $this */

/** @var Doctrine\ORM\EntityManager $em */
$em = $this->getEntityManager();

/**
 * Auto-generated Migration: Please modify to your needs!
 */

$sql = <<<SQL
<sql>
SQL;

$this->em->getConnection()->executeQuery($sql);
';

    private $em;
    private $logger;
    private $manager;

    public function __construct(EntityManager $em, Logger $logger, Manager $manager)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->manager = $manager;
    }

    public function isUpToDate() {
        return $this->manager->isUpToDate();
    }

    public function getQueriesToDump()
    {
        $conn = $this->em->getConnection();
        $platform = $conn->getDatabasePlatform();

        $fromSchema = $conn->getSchemaManager()->createSchema();
        $toSchema = $this->getSchemaProvider()->createSchema();

        $queries = $fromSchema->getMigrateToSql($toSchema, $platform);
        $queriesToDump = [];
        $supportedTables = $this->getSupportedTables();
        foreach ($queries as $query) {
            foreach ($supportedTables as $tableName => $bundle) {
                if (stripos($query, $tableName) !== false) {
                    if (!array_key_exists($bundle->getName(), $queriesToDump)) {
                        $queriesToDump[$bundle->getName()] = [];
                    }
                    $queriesToDump[$bundle->getName()][] = [$bundle, $query . ";"];
                    break 1;
                }
            }
        }

        return $queriesToDump;
    }

    private function getSchemaProvider()
    {
        return new OrmSchemaProvider($this->em);
    }

    private function getSupportedTables()
    {
        $supportedTables = [];

        $meta = $this->em->getMetadataFactory()->getAllMetadata();
        $bundles = $this->manager->getMigratableBundles();
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $m */
        foreach ($meta as $m) {
            foreach ($bundles as $bundle) {
                if (strpos($m->getName(), $bundle->getNamespace()) === 0) {
                    $supportedTables[$m->getTableName()] = $bundle;
                    break 1;
                }
            }
        }

        return $supportedTables;
    }


    /**
     * Build the sql file for the given bundle
     *
     * @param $nextVersion
     * @param MigratableInterface $bundle
     * @param array $queriesToDump
     */
    public function buildMigrationFile($nextVersion, MigratableInterface $bundle, array $queriesToDump)
    {
        if (!file_exists($bundle->getMigrationPath())) {
            mkdir($bundle->getMigrationPath(), 0777, true);
        }

        if (empty($bundle->getVersion())) {
            $migrationFilename = MigrationUtil::MIGRATION_INSTALL . '-' . $nextVersion . '.php';
        } else {
            $migrationFilename = MigrationUtil::MIGRATION_UPGRADE . '-' . $bundle->getVersion() . '-' . $nextVersion . '.php';
        }

        $namespace = $this->manager->getInstallerNamespace();
        $sqlToRun = join("\r\n", $queriesToDump);
        $code = str_replace(['<namespace>', '<sql>'], [$namespace, $sqlToRun], self::$_migration_template);

        file_put_contents($bundle->getMigrationPath() . DIRECTORY_SEPARATOR . $migrationFilename, $code);
    }

    /**
     * Updates the "getVersion" method for the given bundle class file
     *
     * @param MigratableInterface $bundle
     * @param string $nextVersion
     */
    public function updateBundleClassFile($bundle, $nextVersion)
    {
        // Mise a jour de la version du bundle
        $reflected = new \ReflectionObject($bundle);
        $bundleFilename = $reflected->getFileName();

        $bundleContent = file_get_contents($bundleFilename);

        $code = str_replace(['<version>'], [$nextVersion], self::$_bundle_version_template);
        $code = preg_replace('/^ +$/m', '', $code);
        $newContent = preg_replace("/public function getVersion\(\)\s*\{\s*.*\s*\}/", $code, $bundleContent);
        file_put_contents($bundleFilename, $newContent);
    }
}