<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 29/05/2017
 * Time: 22:27
 */

namespace Mgi\CoreBundle\Util;

use Symfony\Component\Finder\Finder;

/**
 * Class MigrationUtil
 * @package Mgi\CoreBundle\MigrationUtil
 */
class MigrationUtil
{
    const MIGRATION_INSTALL = "install";
    const MIGRATION_UPGRADE = "upgrade";
    const VERSION_PATTERN = "[a-zA-Z0-9\.]*";

    /**
     * @param string $path
     *
     * @return array
     */
    static function buildMigrationGraph($path)
    {
        $versionFiles = [];

        $finder = new Finder();
        $files = $finder->files()->in($path)->name('*.php');
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($files as $file) {
            list($from, $to) = self::getVersion($file->getFilename());
            if ($from === $to) {
                $from = 0;
            }
            $versionFiles[$from] = [$to, $file->getRealPath()];
        }

        return $versionFiles;
    }

    /**
     * Returns the next version number from filename
     *
     * @param string $filename
     * @return array
     */
    static function getVersion($filename)
    {
        $versionFrom = $versionTo = "";

        preg_match("/(" . self::MIGRATION_INSTALL . "|" . self::MIGRATION_UPGRADE . ")-?(" . self::VERSION_PATTERN . ")-?(" . self::VERSION_PATTERN . ").php/", $filename, $matches);

        $matchesCount = count($matches);
        if ($matchesCount > 1) {
            $versionFromIndex = ($matches[1] === self::MIGRATION_INSTALL) ? 2 : 1;
            $versionFrom = $matches[2];
            $versionTo = $matches[$matchesCount - $versionFromIndex];
        }

        return [$versionFrom, $versionTo];
    }
}