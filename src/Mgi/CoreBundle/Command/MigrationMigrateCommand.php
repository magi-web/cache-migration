<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 16/06/2017
 * Time: 23:31
 */

namespace Mgi\CoreBundle\Command;

use Mgi\CoreBundle\Migration\ImportManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrationMigrateCommand
 * @package Mgi\CoreBundle\Command
 */
class MigrationMigrateCommand extends Command
{

    /** @var ImportManager $exportMigrationManager */
    private $importMigrationManager;

    /**
     * MigrationMigrateCommand constructor.
     * @param ImportManager $migrationManager
     */
    public function __construct(ImportManager $migrationManager)
    {
        $this->importMigrationManager = $migrationManager;

        // you *must* call the parent constructor
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('doctrine:migrations:migrate');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->importMigrationManager->doMigrations();
    }
}