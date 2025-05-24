<?php

namespace App\Trait;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait DateFilterTrait
{
    /**
     * Procesa los parámetros de fecha de una solicitud
     * 
     * @param Request $request La solicitud HTTP
     * @return array Un array con las fechas procesadas o errores
     */
    protected function processDateFilters(Request $request): array
    {
        $startDate = null;
        $endDate = null;
        $error = null;
        
        if ($request->query->has('start_date')) {
            try {
                $startDate = new \DateTimeImmutable($request->query->get('start_date'));
            } catch (\Exception $e) {
                $error = [
                    'error' => 'Formato de fecha de inicio inválido. Use el formato YYYY-MM-DD.',
                    'status' => Response::HTTP_BAD_REQUEST
                ];
                return ['startDate' => null, 'endDate' => null, 'error' => $error];
            }
        }
        
        if ($request->query->has('end_date')) {
            try {
                $endDate = new \DateTimeImmutable($request->query->get('end_date'));
                // Ajustar la fecha de fin para incluir todo el día
                $endDate = $endDate->modify('+1 day')->modify('-1 second');
            } catch (\Exception $e) {
                $error = [
                    'error' => 'Formato de fecha de fin inválido. Use el formato YYYY-MM-DD.',
                    'status' => Response::HTTP_BAD_REQUEST
                ];
                return ['startDate' => null, 'endDate' => null, 'error' => $error];
            }
        }
        
        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'error' => null
        ];
    }
    
    /**
     * Crea una respuesta JSON de error para fechas inválidas
     * 
     * @param array $error Información del error
     * @return JsonResponse
     */
    protected function createDateErrorResponse(array $error): JsonResponse
    {
        return new JsonResponse(['error' => $error['error']], $error['status']);
    }
}