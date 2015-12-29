<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserSuspendCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('user:suspend')
            ->setDescription('Suspend specified user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
