<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Payment;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;

#[Route('/api/payments')]
#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository
    ) {}

    #[Route('/process/{id}', name: 'app_payment_process', methods: ['POST'])]
    public function processPayment(#[CurrentUser] User $user, int $id, Request $request): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return $this->json([
                'error' => 'Pedido no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // Verificar que el pedido pertenece al usuario actual
        if ($order->getUser()->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json([
                'error' => 'No tienes permiso para pagar este pedido'
            ], Response::HTTP_FORBIDDEN);
        }

        // Verificar que el pedido está en estado pendiente
        if ($order->getStatus() !== 'pending') {
            return $this->json([
                'error' => 'Este pedido no puede ser pagado en su estado actual'
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);

        // Validar datos de pago (simulado)
        if (!isset($data['payment_method']) || !isset($data['card_number'])) {
            return $this->json([
                'error' => 'Faltan datos de pago'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validación básica del número de tarjeta (simulado)
        $cardNumber = preg_replace('/\s+/', '', $data['card_number']);
        if (!preg_match('/^\d{16}$/', $cardNumber)) {
            return $this->json([
                'error' => 'Número de tarjeta inválido'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Crear registro de pago
            $payment = new Payment();
            $payment->setOrder($order);
            $payment->setAmount($order->getTotalAmount());
            $payment->setPaymentMethod($data['payment_method']);
            // Almacenar solo los últimos 4 dígitos por seguridad
            $payment->setCardLastFour(substr($cardNumber, -4));
            $payment->setStatus('completed');
            $payment->setTransactionId('SIM_' . uniqid());
            $payment->setCreatedAt(new \DateTimeImmutable());

            // Actualizar estado del pedido
            $order->setStatus('paid');
            $order->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($payment);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Pago procesado correctamente',
                'payment' => [
                    'id' => $payment->getId(),
                    'transaction_id' => $payment->getTransactionId(),
                    'amount' => $payment->getAmount(),
                    'status' => $payment->getStatus(),
                    'created_at' => $payment->getCreatedAt()->format('Y-m-d H:i:s')
                ],
                'order' => [
                    'id' => $order->getId(),
                    'status' => $order->getStatus()
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al procesar el pago: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/history', name: 'app_payment_history', methods: ['GET'])]
    public function paymentHistory(#[CurrentUser] User $user): JsonResponse
    {
        // Obtener todos los pagos del usuario
        $payments = $this->entityManager->getRepository(Payment::class)
            ->findByUser($user);

        $paymentsData = [];
        foreach ($payments as $payment) {
            $paymentsData[] = [
                'id' => $payment->getId(),
                'transaction_id' => $payment->getTransactionId(),
                'amount' => $payment->getAmount(),
                'payment_method' => $payment->getPaymentMethod(),
                'card_last_four' => $payment->getCardLastFour(),
                'status' => $payment->getStatus(),
                'created_at' => $payment->getCreatedAt()->format('Y-m-d H:i:s'),
                'order_id' => $payment->getOrder()->getId()
            ];
        }

        return $this->json([
            'payments' => $paymentsData
        ]);
    }
}
