<?php

namespace Xsanisty\UserManager\Command;

use SilexStarter\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use Exception;

class UserCreateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Create new user in database')
            ->addOption(
                'login-email',
                'e',
                InputOption::VALUE_REQUIRED,
                'The user email address used for login'
            )
            ->addOption(
                'password',
                'p',
                InputOption::VALUE_REQUIRED,
                'The user password'
            )
            ->addOption(
                'first-name',
                'f',
                InputOption::VALUE_REQUIRED,
                'The user first name'
            )
            ->addOption(
                'last-name',
                'l',
                InputOption::VALUE_REQUIRED,
                'The user last name'
            )
            ->addOption(
                'admin',
                'a',
                InputOption::VALUE_NONE,
                'Give user administrator right'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app    = $this->getSilexStarter();
        $email  = $input->getOption('login-email');
        $passwd = $input->getOption('password');

        if ($email && $passwd) {
            $isAdmin    = $input->getOption('admin');
            $userData   = [
                'email'         => $email,
                'password'      => $passwd,
                'first_name'    => (string) $input->getOption('first-name'),
                'last_name'     => (string) $input->getOption('last-name'),
                'activated'     => 1,
                'permissions'   => $isAdmin ? ['admin' => 1] : []
            ];
        } else {
            $userData   = $this->populateUserData($input, $output);
        }

        $app['sentry']->register($userData);

        $output->writeln('<info>'.$userData['first_name'].' is now registered, she/he can now login using \''.$userData['email'].'\' as user id!</info>');
    }

    protected function populateUserData(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $fName  = $helper->ask($input, $output, new Question('What is her/his first name? '));
        $lName  = $helper->ask($input, $output, new Question('And her/his last name? '));
        $email  = $helper->ask($input, $output, new Question('Please provide email address for login: '));
        $admin  = $helper->ask($input, $output, new Question('Should this user become administrator? <comment>[y/N]</comment> ', false));
        $passwd = $helper->ask($input, $output, (new Question('Please provide the password: '))->setHidden(true));
        $confirm= $helper->ask($input, $output, (new Question('Can you retype the password just to be sure? '))->setHidden(true));

        if ($passwd !== $confirm) {
            throw new Exception("Sorry, but it seems you make mistake when typing your password");
        }

        $user = [
            'email' => $email,
            'password' => $passwd,
            'first_name' => (string) $fName,
            'last_name' => (string) $lName,
            'activated' => 1
        ];

        if ($admin) {
            $user['permissions'] = ['admin' => 1];
        }

        return $user;
    }
}
