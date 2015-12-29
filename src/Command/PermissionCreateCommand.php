<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PermissionCreateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('permission:create')
            ->setDescription('Create new permission in database')
            ->addArgument(
                'permission-name',
                InputArgument::REQUIRED,
                'The permission name'
            )
            ->addArgument(
                'description',
                InputArgument::REQUIRED,
                'The permission description'
            )
            ->addOption(
                'category',
                'c',
                InputOption::VALUE_REQUIRED,
                'The permission category'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app    = $this->getSilexStarter();
        $name   = $input->getArgument('permission-name');
        $desc   = $input->getArgument('description');
        $cat    = $input->getOption('category');

        $app['Xsanisty\UserManager\Repository\PermissionRepository']->create(
            [
                'name'          => $name,
                'category'      => (string) $cat,
                'description'   => (string) $desc
            ]
        );

    }
}
