<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class UserListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('user')
            ->setDescription('Display all registered users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app   = $this->getSilexStarter();
        $table = new Table($output);
        $rows  = [];

        foreach ($app['Xsanisty\UserManager\Repository\UserRepository']->findAll() as $user) {
            $rows[] = [
                $user->id,
                $user->first_name,
                $user->last_name,
                $user->email
            ];
        }

        $table->setHeaders(['ID', 'First Name', 'Last Name', 'Email']);
        $table->setRows($rows);

        $table->render();

    }
}
