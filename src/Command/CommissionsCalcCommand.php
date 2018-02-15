<?php

namespace App\Command;

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
        $output->writeln('Done calculate');
    }
}
