<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GroupListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('group')
            ->setDescription('Display all available group');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
