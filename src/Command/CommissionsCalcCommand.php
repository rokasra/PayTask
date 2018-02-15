<?php

namespace App\Command;

use App\Service\CommissionsManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
        $this->setName('app:commissions-calculate');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var CommissionsManager $manager */
        $manager = $this->getContainer()->get('service.commissions_manager');
        $manager->calculate();

        $output->writeln('Done calculate');
    }
}
