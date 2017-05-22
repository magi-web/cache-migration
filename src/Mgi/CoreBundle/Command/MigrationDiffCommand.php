<?php

namespace Mgi\CoreBundle\Command;

use Mgi\CoreBundle\Migration\ExportManager;
use Mgi\CoreBundle\Migration\MigratableInterface;
use Mgi\CoreBundle\Util\MigrationUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MigrationDiffCommand extends Command
{
    /** @var ExportManager $exportMigrationManager */
    private $exportMigrationManager;

    /**
     * MigrationDiffCommand constructor.
     * @param ExportManager $migrationManager
     */
    public function __construct(ExportManager $migrationManager)
    {
        $this->exportMigrationManager = $migrationManager;

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
            ->setName('doctrine:migrations:diff');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->exportMigrationManager->isUpToDate()) {
            throw new \Exception("The resource table seems not up-to-date. Please import the new migrations files first");
        }

        $bundlesToMigrate = $this->getBundlesToMigrate($input, $output);

        $this->generateBundlesMigration($bundlesToMigrate);
    }

    /**
     * Returns an associative array of the bundles to migrate
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    private function getBundlesToMigrate(InputInterface $input, OutputInterface $output)
    {
        $bundlesToMigrate = [];

        $queriesToDump = $this->exportMigrationManager->getQueriesToDump();
        foreach (array_keys($queriesToDump) as $bundleName => list($bundle, $queries)) {
            $nextVersion = $this->getNextVersionForBundle($bundle, $input, $output);
            $bundlesToMigrate[$bundleName] = [$nextVersion, $bundle, $queries];
        }

        return $bundlesToMigrate;
    }

    /**
     * Asks for the given bundle's next version
     *
     * @param MigratableInterface $bundle
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    private function getNextVersionForBundle(MigratableInterface $bundle, InputInterface $input, OutputInterface $output)
    {
        $question = new Question('Please enter the next version of the ' . $bundle->getName() . ' bundle (currently \'' . $bundle->getVersion() . '\') : ', '');
        $question->setNormalizer(function ($value) {
            // $value can be null here
            return $value ? trim($value) : '';
        });
        $question->setValidator(function ($answer) use ($bundle) {
            preg_match("/" . MigrationUtil::VERSION_PATTERN . "/", $answer, $matches);

            if ($bundle->getVersion() === $answer) {
                throw new \RuntimeException(
                    'The version should be different from the current bundle\'s one.'
                );
            }
            if (!is_string($answer) || empty($answer) || empty($matches) || $matches[0] !== $answer) {
                throw new \RuntimeException(
                    'The version should match the current regex : ' . MigrationUtil::VERSION_PATTERN
                );
            }

            return $answer;
        });

        $helper = $this->getHelper('question');
        return $helper->ask($input, $output, $question);
    }

    /**
     * Handles the bundles migration
     *
     * @param array $bundlesToMigrate
     */
    private function generateBundlesMigration(array $bundlesToMigrate)
    {
        /**
         * @var string $bundleName
         * @var string $nextVersion
         * @var MigratableInterface $bundle
         * @var array $queriesToDump
         */
        foreach ($bundlesToMigrate as $bundleName => list($nextVersion, $bundle, $queriesToDump)) {
            $this->exportMigrationManager->buildMigrationFile($nextVersion, $bundle, $queriesToDump);

            $this->exportMigrationManager->updateBundleClassFile($bundle, $nextVersion);
        }
    }


}
