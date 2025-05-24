<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {}

    #[Route('/users', name: 'app_admin_list_users', methods: ['GET'])]
    public function listUsers(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        $usersData = [];
        foreach ($users as $user) {
            $usersData[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'role' => $user->getRole(),
                'address' => $user->getAddress(),
                'phone' => $user->getPhone(),
                'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s')
            ];
        }

        return $this->json([
            'users' => $usersData
        ]);
    }

    #[Route('/users/{id}', name: 'app_admin_get_user', methods: ['GET'])]
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json([
                'error' => 'Usuario no encontrado'
            ], Response::HTTP_NOT_FOUND);
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
        ]);
    }

    #[Route('/users/{id}/change-role', name: 'app_admin_change_user_role', methods: ['PUT'])]
    public function changeUserRole(Request $request, int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json([
                'error' => 'Usuario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->getUser();
        // Evitar que un admin cambie su propio rol
        if ($user->getId() === ($currentUser instanceof User ? $currentUser->getId() : null)) {
            return $this->json([
                'error' => 'No puedes cambiar tu propio rol'
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['role']) || !in_array($data['role'], [User::ROLE_USER, User::ROLE_ADMIN])) {
            return $this->json([
                'error' => 'Rol no válido'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user->setRole($data['role']);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Rol actualizado correctamente',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getName(),
                    'role' => $user->getRole()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al actualizar el rol'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/users/{id}', name: 'app_admin_delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json([
                'error' => 'Usuario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // Evitar que un admin se elimine a sí mismo
        $currentUser = $this->getUser();
        if ($user->getId() === ($currentUser instanceof User ? $currentUser->getId() : null)) {
            return $this->json([
                'error' => 'No puedes eliminar tu propia cuenta'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Usuario eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al eliminar el usuario'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/statistics', name: 'app_admin_user_statistics', methods: ['GET'])]
    public function getUserStatistics(Request $request): JsonResponse
    {
        // Verificar si se proporcionan fechas de inicio y fin
        $startDate = null;
        $endDate = null;
        
        if ($request->query->has('start_date')) {
            try {
                $startDate = new \DateTimeImmutable($request->query->get('start_date'));
            } catch (\Exception $e) {
                return $this->json([
                    'error' => 'Formato de fecha de inicio inválido. Use el formato YYYY-MM-DD.'
                ], Response::HTTP_BAD_REQUEST);
            }
        }
        
        if ($request->query->has('end_date')) {
            try {
                $endDate = new \DateTimeImmutable($request->query->get('end_date'));
                // Ajustar la fecha de fin para incluir todo el día
                $endDate = $endDate->modify('+1 day')->modify('-1 second');
            } catch (\Exception $e) {
                return $this->json([
                    'error' => 'Formato de fecha de fin inválido. Use el formato YYYY-MM-DD.'
                ], Response::HTTP_BAD_REQUEST);
            }
        }
        
        $statistics = $this->userRepository->getStatistics($startDate, $endDate);
        
        return $this->json([
            'statistics' => $statistics
        ]);
    }
}

