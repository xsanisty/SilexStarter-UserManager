<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PermissionAddCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('permission:add')
            ->setDescription('Add new permission into database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
