<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class GroupListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('group:all')
            ->setDescription('Display all available group');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app   = $this->getSilexStarter();
        $table = new Table($output);
        $rows  = [];

        foreach ($app['Xsanisty\UserManager\Repository\GroupRepository']->findAll() as $group) {
            $rows[] = [
                $group->id,
                $group->name,
                $group->description,
                implode(', ', array_keys($group->permissions))
            ];
        }

        if (!$rows) {
            $output->writeln('<info>No group registered</info>');
        } else {

            $table->setHeaders(['ID', 'Name', 'Description', 'Permissions']);
            $table->setRows($rows);

            $table->render();
        }
    }
}
