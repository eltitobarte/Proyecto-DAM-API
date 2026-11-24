<?php

namespace App\Controller;

use App\Repository\UsuariosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UsuariosRepository $usuariosRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $payload = json_decode((string) $request->getContent(), true);

        if (!\is_array($payload)) {
            return $this->json([
                'message' => 'El cuerpo de la petición debe ser un JSON válido.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Se admiten tanto los nombres de campo usados por la app (email/password)
        // como los nombres de las entidades (correoElectronico/contrasenya).
        $email = $payload['email'] ?? $payload['correoElectronico'] ?? null;
        $password = $payload['password'] ?? $payload['contrasenya'] ?? null;

        if (!\is_string($email) || !\is_string($password) || $email === '' || $password === '') {
            return $this->json([
                'message' => 'Debes indicar el correo electrónico y la contraseña.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $usuario = $usuariosRepository->findOneByCorreoElectronico(trim($email));

        if (null === $usuario || !$passwordHasher->isPasswordValid($usuario, $password)) {
            return $this->json([
                'message' => 'Usuario o contraseña incorrecto',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Como nombre y apellidos están en las clases hijas, los extraemos de forma segura
        $nombre = method_exists($usuario, 'getNombre') ? $usuario->getNombre() : null;
        $apellidos = method_exists($usuario, 'getApellidos') ? $usuario->getApellidos() : null;

        return $this->json([
            'id' => $usuario->getId(),
            'correoElectronico' => $usuario->getCorreoElectronico(),
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'roles' => $usuario->getRoles(),
        ], Response::HTTP_OK);
    }
}
