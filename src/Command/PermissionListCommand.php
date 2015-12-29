<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class PermissionListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('permission:all')
            ->setDescription('Display all available permission');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app   = $this->getSilexStarter();
        $table = new Table($output);
        $rows  = [];

        foreach ($app['Xsanisty\UserManager\Repository\PermissionRepository']->findAll() as $group) {
            $rows[] = [
                $group->name,
                $group->category,
                $group->description
            ];
        }

        if (!$rows) {
            $output->writeln('<info>No permission registered</info>');
        } else {

            $table->setHeaders(['Name', 'Category', 'Description']);
            $table->setRows($rows);

            $table->render();
        }
    }
}
