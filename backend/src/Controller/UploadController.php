<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/upload', name: 'app_upload_')]
class UploadController extends AbstractController
{
    #[Route('/image', name: 'image', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function uploadImage(Request $request): JsonResponse
    {
        $file = $request->files->get('image');
        
        if (!$file) {
            return $this->json([
                'error' => 'No se ha proporcionado ninguna imagen'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Validar que sea una imagen
        $mimeType = $file->getMimeType();
        if (!str_starts_with($mimeType, 'image/')) {
            return $this->json([
                'error' => 'El archivo proporcionado no es una imagen válida'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Generar un nombre único para el archivo
        $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        
        // Obtener el directorio de imágenes
        $uploadDir = $this->getParameter('images_directory');
        
        // Verificar que el directorio existe, si no, crearlo
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        try {
            // Mover el archivo al directorio de imágenes
            $file->move($uploadDir, $fileName);
            
            return $this->json([
                'success' => true,
                'imagePath' => $fileName
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Registrar el error para depuración
            error_log('Error al subir imagen: ' . $e->getMessage());
            
            return $this->json([
                'error' => 'Error al guardar la imagen: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/test-directory', name: 'test_directory', methods: ['GET'])]
    public function testDirectory(): JsonResponse
    {
        $uploadDir = $this->getParameter('images_directory');
        
        $info = [
            'directory' => $uploadDir,
            'exists' => is_dir($uploadDir),
            'writable' => is_writable($uploadDir),
            'free_space' => disk_free_space($uploadDir),
        ];
        
        if (!$info['exists']) {
            try {
                mkdir($uploadDir, 0777, true);
                $info['created'] = true;
                $info['exists'] = is_dir($uploadDir);
                $info['writable'] = is_writable($uploadDir);
            } catch (\Exception $e) {
                $info['error'] = $e->getMessage();
            }
        }
        
        return $this->json($info);
    }
}


