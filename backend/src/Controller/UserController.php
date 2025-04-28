<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/users')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/register', name: 'app_user_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        // Decodificar el JSON recibido
        $data = json_decode($request->getContent(), true);

        // Validar que los campos requeridos existen
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            return $this->json([
                'error' => 'Los campos email, password y name son obligatorios'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validar formato de email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'error' => 'El formato del email no es válido'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verificar si el email ya existe
        $existingUser = $this->userRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json([
                'error' => 'El email ya está registrado'
            ], Response::HTTP_CONFLICT);
        }

        try {
            // Crear nuevo usuario
            $user = new User();
            $user->setEmail($data['email']);
            $user->setName($data['name']);
            
            // Hashear la contraseña
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $data['password']
            );
            $user->setPassword($hashedPassword);
            
            // Establecer valores adicionales
            $user->setRole('ROLE_USER');
            $user->setCreatedAt(new \DateTimeImmutable());
            
            // Opcional: establecer dirección y teléfono si se proporcionan
            if (isset($data['address'])) {
                $user->setAddress($data['address']);
            }
            if (isset($data['phone'])) {
                $user->setPhone($data['phone']);
            }

            // Guardar en la base de datos
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Usuario registrado correctamente',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getName(),
                    'role' => $user->getRole()
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al registrar el usuario'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/login', name: 'app_user_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        // TODO: Implementar login
        return $this->json(['message' => 'Not implemented yet'], Response::HTTP_NOT_IMPLEMENTED);
    }

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    public function profile(#[CurrentUser] ?User $user): JsonResponse
    {
        // TODO: Implementar obtención de perfil
        return $this->json(['message' => 'Not implemented yet'], Response::HTTP_NOT_IMPLEMENTED);
    }

    #[Route('/profile', name: 'app_user_profile_update', methods: ['PUT'])]
    public function updateProfile(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        // TODO: Implementar actualización de perfil
        return $this->json(['message' => 'Not implemented yet'], Response::HTTP_NOT_IMPLEMENTED);
    }
}

