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
            ->setDescription('Remove permission from database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
