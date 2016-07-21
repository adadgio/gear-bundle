<?php

namespace Adadgio\GearBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NodeRedInstallCommand extends ContainerAwareCommand
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName('adadgio:nodered:install')
            ->addOption(
                'output',
                null,
                InputOption::VALUE_OPTIONAL,
                'Flows json file output directory',
                null
            )
            ->setDescription('Create NodeRed flows')
        ;
    }
    
    /**
     * Execute command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $this->builder = $container->get('adadgio_gear.nodered.flow_builder');

        $this->builder
            ->installFlows();

        // env vars need to be set:
        // SYMFONY__HTTP__PROTOCOL = "http.protocol"
        // SYMFONY__HTTP__HOST = "http.host"
        $protocol = '';
        $host = '';

        // say something before dying
        $output->writeln('');
        $output->writeln(sprintf('Flows were saved to <fg=green;options=bold></>'));
    }
}
