<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PermissionRemoveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('permission:remove')
            ->setDescription('Remove permission from database')
            ->addArgument(
                'permission-name',
                InputArgument::REQUIRED,
                'The permission name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app        = $this->getSilexStarter();
        $permName   = $input->getArgument('permission-name');
        $perm       = $app['Xsanisty\UserManager\Repository\PermissionRepository']->findByName($permName);

        $app['Xsanisty\UserManager\Repository\PermissionRepository']->delete($perm->id);

        $output->writeln('<info>Permission "'.$perm->name.'" is now removed</info>');
    }
}
