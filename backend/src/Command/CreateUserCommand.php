<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Crea un nuevo usuario',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email del usuario')
            ->addArgument('password', InputArgument::REQUIRED, 'Contraseña del usuario')
            ->addArgument('name', InputArgument::REQUIRED, 'Nombre del usuario')
            ->addArgument('role', InputArgument::OPTIONAL, 'Rol del usuario (ROLE_USER o ROLE_ADMIN)', User::ROLE_USER)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $name = $input->getArgument('name');
        $role = $input->getArgument('role');

        // Validar el rol
        if (!in_array($role, [User::ROLE_USER, User::ROLE_ADMIN])) {
            $io->error('Rol no válido. Debe ser ROLE_USER o ROLE_ADMIN');
            return Command::FAILURE;
        }

        // Verificar si el usuario ya existe
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error('Ya existe un usuario con ese email');
            return Command::FAILURE;
        }

        // Crear el usuario
        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setRole($role);
        $user->setCreatedAt(new \DateTimeImmutable());

        // Hashear la contraseña
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Guardar en la base de datos
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Usuario creado correctamente');
        $io->table(
            ['ID', 'Email', 'Nombre', 'Rol'],
            [[$user->getId(), $user->getEmail(), $user->getName(), $user->getRole()]]
        );

        return Command::SUCCESS;
    }
}
