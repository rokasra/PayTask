<?php

namespace App\Command;

use App\Service\CommissionsManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * App\Command\CommissionsCalcCommand
 */
class CommissionsCalcCommand extends ContainerAwareCommand
{
    /**
     * configure command
     */
    protected function configure()
    {
        $this->setName('app:commissions-calculate')
            ->addArgument('file', InputArgument::REQUIRED, 'Link to file, example: public/input.csv');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var CommissionsManager $manager */
        $manager = $this->getContainer()->get('service.commissions_manager');

        $file = $input->getArgument('file');
        $data = $this->parseCsvFile($file);
        $manager->calculate($data);

        $output->writeln('Done calculate');
    }

    /**
     * @param string $file
     *
     * @return array
     */
    protected function parseCsvFile($file)
    {
        $csvFile = fopen($file, 'r');
        $data = [];
        while (!feof($csvFile)) {
            $data[] = fgetcsv($csvFile, null, ',');
        }

        return $data;
    }
}
