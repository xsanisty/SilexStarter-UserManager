<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserRemoveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('user:remove')
            ->setDescription('Remove user from database')
            ->addArgument(
                'user-login',
                InputArgument::REQUIRED,
                'The user login'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app        = $this->getSilexStarter();
        $userLogin  = $input->getArgument('user-login');
        $user       = $app['Xsanisty\UserManager\Repository\UserRepository']->findByLogin($userLogin);

        $app['Xsanisty\UserManager\Repository\UserRepository']->delete($user->id);

        $output->writeln('<info>User with email "'.$user->email.'" is now removed</info>');
    }
}
