<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GroupAddCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('group:add')
            ->setDescription('Add new group into database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
