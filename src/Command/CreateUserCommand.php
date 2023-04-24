<?php

namespace App\Command;

use App\Entity\Octopus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Create a new octopus user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new Octopus();

        $output->writeln('');

        $email = $this->askEmail($input, $output);
        $name = $this->askName($input, $output);
        $password = $this->askPassword($input, $output);

        $user
            ->setEmail($email)
            ->setName($name)
            ->setPassword($this->passwordHasher->hashPassword($user, $password))
        ;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('');
        $output->writeln(sprintf('<info>The octopus %s has been created successfully!</info>', $email));
        $output->writeln('');

        return Command::SUCCESS;
    }

    protected function askEmail(InputInterface $input, OutputInterface $output): mixed
    {
        $helper = $this->getHelper('question');
        $question = new Question('Email: ');

        $question->setValidator(function ($answer) {
            if (!is_string($answer)) {
                throw new \RuntimeException('The email can not be empty.');
            }

            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('The email is not valid.');
            }

            if ($this->entityManager->getRepository(Octopus::class)->findOneBy(['email' => $answer])) {
                throw new \RuntimeException('The email is already used.');
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    protected function askName(InputInterface $input, OutputInterface $output): mixed
    {
        $helper = $this->getHelper('question');
        $question = new Question('Name: ');

        $question->setValidator(function ($answer) {
            if (!is_string($answer)) {
                throw new \RuntimeException('The name can not be empty.');
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    protected function askPassword(InputInterface $input, OutputInterface $output): mixed
    {
        $helper = $this->getHelper('question');
        $question = new Question('Password: ');

        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $question->setValidator(function ($answer) {
            if (!is_string($answer)) {
                throw new \RuntimeException('The password can not be empty.');
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }
}
