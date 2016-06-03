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
        $inputFlows = __DIR__.'/../Resources/nodered/adadgio.gear.flow.json';

        $outputDir = $input->getOption('output');
        if (null === $outputDir) {
            $outputDir = getcwd();
        }

        $outputFlows = $outputDir.'/'.basename($inputFlows);

        // env vars need to be set:
        // SYMFONY__HTTP__PROTOCOL = "http.protocol"
        // SYMFONY__HTTP__HOST = "http.host"
        $protocol = '';
        $host = '';

        $flowsData = file_get_contents($inputFlows);
        $flowsData = str_replace('%http.protocol%', $protocol, $flowsData);
        $flowsData = str_replace('%http.host%', $host, $flowsData);

        // save new flows
        file_put_contents($outputFlows, $flowsData);

        // say something before dying
        $output->writeln('');
        $output->writeln(sprintf('Flows were saved to <fg=green;options=bold>%s</>', $outputDir));
    }
}
