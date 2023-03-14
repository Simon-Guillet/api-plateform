<?php


namespace App\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'Creates the first user (admin). If a user already exists in database, nothing happens.';

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->em = $entityManager;
        $this->hasher = $hasher;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $questionLogin = new Question('username ? ');
        $questionPassword = new Question('password ? ');
        $questionPassword->setHidden(true);
        $questionPassword->setHiddenFallback(false);

        $questionLastName = new Question('last name ? ');
        $questionFirstName = new Question('first name ? ');

        $login = $helper->ask($input, $output, $questionLogin);
        $password = $helper->ask($input, $output, $questionPassword);
        $lastName = $helper->ask($input, $output, $questionLastName);
        $firstName = $helper->ask($input, $output, $questionFirstName);

        $output->writeln('username : ' . $login);
        $output->writeln('password : ' . $password);
        $output->writeln('last name : ' . $lastName);
        $output->writeln('first name : ' . $firstName);
        

        $user = new User();
        $user->setLogin($login);
        $user->setPassword($password);
        $user->setLastName($lastName);
        $user->setFirstName($firstName);
        
        $hash = $this->hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hash);


        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('User created successfully !');
        return Command::SUCCESS;
    }
}
