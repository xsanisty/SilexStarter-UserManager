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
            ->setDescription('Remove group from database')
            ->addArgument(
                'group-name',
                InputArgument::REQUIRED,
                'The group name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app        = $this->getSilexStarter();
        $groupName  = $input->getArgument('group-name');
        $group      = $app['Xsanisty\UserManager\Repository\GroupRepository']->findByName($groupName);

        $app['Xsanisty\UserManager\Repository\GroupRepository']->delete($group->id);

        $output->writeln('<info>Group "'.$group->name.'" is now removed</info>');
    }
}
