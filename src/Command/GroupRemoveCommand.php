<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GroupRemoveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('group:remove')
            ->setDescription('Remove group from database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
