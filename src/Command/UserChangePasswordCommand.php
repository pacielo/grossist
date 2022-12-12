<?php

namespace App\Command;

use App\Repository\UserManagement\UserRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserChangePasswordCommand extends Command
{
    protected static $defaultName = 'user:change-password';
    private $container;
    private $userRepository;
    private $passwordEncoder;

    public function __construct(ContainerInterface $container, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:change-password')
            ->setDescription('Change the password of a user.')
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
            ])
            ->setHelp(
                <<<'EOT'
The <info>fos:user:change-password</info> command changes the password of a user:

  <info>php %command.full_name% matthieu</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $em = $this->container->get('doctrine')->getManager();
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (null === $user) {
            $output->writeln(sprintf('<error>Error</error>: user <comment>%s</comment> not found.', $username));

            return 1;
        }

        if ($password) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $em->persist($user);
            $em->flush();
            $io->note(sprintf('New password: %s', $password));
            $io->success(sprintf('Changed password for user %s', $username));
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('username')) {
            $question = new Question('Please give the username:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \Exception('Username can not be empty');
                }

                return $username;
            });
            $questions['username'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please enter the new password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
