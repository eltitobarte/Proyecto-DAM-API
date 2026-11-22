<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Repository\UsuariosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class SecurityController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UsuariosRepository $usuario, #[CurrentUser] ?Usuarios $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'Credenciales inválidas o cabecera Content-Type: application/json ausente.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $payload = json_decode((string) $request->getContent(), true);
        $email = \is_array($payload) && \is_string($payload['email'] ?? null) ? $payload['email'] : '';
        $password = \is_array($payload) && \is_string($payload['password'] ?? null) ? $payload['password'] : '';

        if (!$usuario->login($email, $password)) {
            return $this->json([
                'message' => 'Usuario o contraseña incorrecto'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Como nombre y apellidos están en las clases hijas, los extraemos de forma segura
        $nombre = method_exists($user, 'getNombre') ? $user->getNombre() : null;
        $apellidos = method_exists($user, 'getApellidos') ? $user->getApellidos() : null;

        return $this->json([
            'id' => $user->getId(),
            'correoElectronico' => $user->getUserIdentifier(),
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'roles' => $user->getRoles(),
        ], Response::HTTP_OK);
    }
}
