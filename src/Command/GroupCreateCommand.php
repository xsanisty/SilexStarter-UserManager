<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class GroupCreateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('group:create')
            ->setDescription('Create new user group in database')
            ->addArgument(
                'group-name',
                InputArgument::REQUIRED,
                'The group name'
            )
            ->addOption(
                'description',
                'd',
                InputOption::VALUE_REQUIRED,
                'The group description'
            )
            ->addOption(
                'permission',
                'p',
                InputOption::VALUE_REQUIRED,
                'The group description'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app    = $this->getSilexStarter();
        $group  = $input->getArgument('group-name');
        $desc   = $input->getOption('description');

        if (!$input->getOption('permission')) {
            $permCollection = $app['Xsanisty\UserManager\Repository\PermissionRepository']->findAll();
            $permissions    = [];

            foreach ($permCollection as $permission) {
                $permissions[] = $permission->name;
            }

            $helper     = $this->getHelper('question');
            $question   = new ChoiceQuestion(
                'Please select group permissions',
                $permissions
            );
            $question->setMultiselect(true);

            $perms = $helper->ask($input, $output, $question);
        } else {
            $perms  = explode(',', $input->getOption('permission'));
        }

        $app['Xsanisty\UserManager\Repository\GroupRepository']->create(
            [
                'name'          => $group,
                'permissions'   => $perms,
                'description'   => (string) $desc
            ]
        );

        $output->writeln("Group '$group' is created sucessfully");
    }
}
