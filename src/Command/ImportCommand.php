<?php

namespace con4gis\CoreBundle\Command;

use con4gis\CoreBundle\Classes\C4GImport;
use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportCommand extends Command
{
    protected static $defaultName = 'con4gis:import';
    private ContainerInterface $container;

    /**
     * ImportCommand constructor.
     * @param ContaoFramework $framework
     */
    public function __construct(
        ContaoFramework $framework
    ) {
        parent::__construct();
        $framework->initialize();
        $this->container = System::getContainer();
    }

    /**
     * Configures the command to import data to database.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('importId', InputArgument::OPTIONAL, 'Id of Import', null)
            ->setDescription('Import Data')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $importId = (int) $input->getArgument('importId');
        $import = new C4GImport();
        try {
            $import->importBaseData($importId, $output);
        }
        catch (\Exception $exception) {
            C4gLogModel::addLogEntry(
                'core',
                'Error while executing SQL-Import: ' . $exception->getMessage()
            );
            $output->writeln($exception->getMessage());
            return 1;
        }

       return 0;
    }
}