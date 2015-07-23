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

class UserAddCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('user:add')
            ->setDescription('Add new user into database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexStarter();
        $userData = $this->populateUserData($input, $output);

        $app['sentry']->register($userData);

        $output->writeln('<info>'.$userData['first_name'].' is now registered, she/he can now login using \''.$userData['email'].'\' as user id!</info>');
    }

    protected function populateUserData(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');

        $firstName = $helper->ask(
            $input,
            $output,
            new Question('What is her/his first name? ')
        );

        $lastName = $helper->ask(
            $input,
            $output,
            new Question('And her/his last name? ')
        );

        $email = $helper->ask(
            $input,
            $output,
            new Question('Please provide email address for login: ')
        );

        $password  = $helper->ask(
            $input,
            $output,
            (new Question('Please provide the password: '))->setHidden(true)
        );

        $confirmPassword  = $helper->ask(
            $input,
            $output,
            (new Question('Can you retype the password just to be sure? '))->setHidden(true)
        );

        if ($password !== $confirmPassword) {
            throw new Exception("Sorry, but it seems you make mistake when typing your password");
        }

        return [
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'activated' => 1
        ];

    }
}
