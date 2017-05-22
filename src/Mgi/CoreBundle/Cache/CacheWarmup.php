<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 22/05/2017
 * Time: 22:07
 */

namespace Mgi\CoreBundle\Cache;

use Mgi\CoreBundle\Migration\ImportManager;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Class CacheWarmup
 * @package Mgi\CoreBundle\Cache
 */
class CacheWarmup implements CacheWarmerInterface
{
    /** @var ImportManager the migration service */
    private $migrationManager;

    /**
     * CacheWarmup constructor.
     * @param ImportManager $migrationManager
     */
    public function __construct(ImportManager $migrationManager)
    {
        $this->migrationManager = $migrationManager;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $this->migrationManager->doMigrations();
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }
}