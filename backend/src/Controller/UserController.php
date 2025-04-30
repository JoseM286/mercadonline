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

#[Route('/api/users')]      // Ruta base para los endpoints de usuarios
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    //Endpoint http://localhost:8080/api/users/register
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
        if (null === $user) {
            return $this->json([
                'error' => 'Credenciales inválidas.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'role' => $user->getRole(),
                // No incluimos la contraseña por seguridad
            ]
        ]);
    }

    #[Route('/logout', name: 'app_user_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // El proceso de logout real es manejado por Symfony Security
        // Solo necesitamos proporcionar una respuesta JSON
        return $this->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    public function profile(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'error' => 'Usuario no autenticado'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'role' => $user->getRole(),
                'address' => $user->getAddress(),
                'phone' => $user->getPhone(),
                'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s')
            ]
        ], Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/profile', name: 'app_user_profile_update', methods: ['PUT'])]
    public function updateProfile(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'error' => 'Usuario no autenticado'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json([
                'error' => 'Datos JSON inválidos'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Actualizar solo los campos permitidos
            if (isset($data['name'])) {
                $user->setName($data['name']);
            }
            if (isset($data['address'])) {
                $user->setAddress($data['address']);
            }
            if (isset($data['phone'])) {
                $user->setPhone($data['phone']);
            }

            // Guardar los cambios
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Perfil actualizado correctamente',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getName(),
                    'role' => $user->getRole(),
                    'address' => $user->getAddress(),
                    'phone' => $user->getPhone(),
                    'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al actualizar el perfil'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/change-password', name: 'app_user_change_password', methods: ['POST'])]
    public function changePassword(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'error' => 'Usuario no autenticado'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['currentPassword']) || !isset($data['newPassword'])) {
            return $this->json([
                'error' => 'Se requiere la contraseña actual y la nueva contraseña'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verificar que la contraseña actual sea correcta
        if (!$this->passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
            return $this->json([
                'error' => 'La contraseña actual no es correcta'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Validar requisitos de la nueva contraseña
        if (strlen($data['newPassword']) < 4) {
            return $this->json([
                'error' => 'La nueva contraseña debe tener al menos 8 caracteres'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Hashear y guardar la nueva contraseña
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $data['newPassword']
            );
            $user->setPassword($hashedPassword);

            $this->entityManager->flush();

            return $this->json([
                'message' => 'Contraseña actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al actualizar la contraseña'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
