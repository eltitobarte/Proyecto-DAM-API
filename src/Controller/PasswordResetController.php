<?php

namespace App\Controller;

use App\Entity\RecuperacionContrasenya;
use App\Repository\RecuperacionContrasenyaRepository;
use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/password')]
final class PasswordResetController extends AbstractApiController
{
    public function __construct(
        private readonly UsuariosRepository $usuarios,
        private readonly RecuperacionContrasenyaRepository $tokens,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
        #[Autowire('%app.reset_password_ttl%')] private readonly int $tokenTtl,
        #[Autowire('%app.frontend_url%')] private readonly string $frontendUrl,
        #[Autowire('%app.mailer_from%')] private readonly string $remitente,
        #[Autowire('%app.mailer_from_name%')] private readonly string $remitenteNombre,
        #[Autowire('%kernel.debug%')] private readonly bool $debug,
    ) {
    }

    #[Route('/forgot', name: 'api_password_forgot', methods: ['POST'])]
    public function forgot(Request $request): JsonResponse
    {
        $datos = $this->jsonBody($request);
        $correo = $datos['correoElectronico'] ?? $datos['email'] ?? null;

        if (!\is_string($correo) || trim($correo) === '') {
            return $this->respuestaErrores([
                'correoElectronico' => ['El correo electrónico es obligatorio.'],
            ]);
        }

        $correo = trim($correo);

        if (filter_var($correo, \FILTER_VALIDATE_EMAIL) === false) {
            return $this->respuestaErrores([
                'correoElectronico' => ['El correo electrónico no es válido.'],
            ]);
        }

        // Respuesta genérica para no revelar si el correo está registrado o no.
        $respuesta = [
            'message' => 'Si el correo existe, se ha enviado un enlace de recuperación de contraseña.',
        ];

        $usuario = $this->usuarios->findOneByCorreoElectronico($correo);

        if ($usuario !== null && $usuario->getCorreoElectronico() !== null) {
            $this->tokens->invalidarPendientesDe($usuario);

            $tokenPlano = bin2hex(random_bytes(32));
            $ahora = new \DateTimeImmutable();

            $registro = (new RecuperacionContrasenya())
                ->setUsuario($usuario)
                ->setTokenHash(hash('sha256', $tokenPlano))
                ->setFechaSolicitud($ahora)
                ->setFechaExpiracion($ahora->modify(sprintf('+%d seconds', $this->tokenTtl)))
                ->setUsado(false);

            $this->entityManager->persist($registro);
            $this->entityManager->flush();

            $enlace = sprintf(
                '%s/reset-password?token=%s',
                rtrim($this->frontendUrl, '/'),
                $tokenPlano
            );

            $this->enviarCorreoRecuperacion(
                (string) $usuario->getCorreoElectronico(),
                method_exists($usuario, 'getNombre') ? $usuario->getNombre() : null,
                $enlace
            );

            // Sin SMTP configurado (MAILER_DSN=null://null) el token se devuelve
            // en la respuesta para poder probar el flujo en desarrollo.
            if ($this->debug) {
                $respuesta['debugToken'] = $tokenPlano;
                $respuesta['debugUrl'] = $enlace;
            }
        }

        return $this->json($respuesta, JsonResponse::HTTP_OK);
    }

    #[Route('/reset/{token}', name: 'api_password_reset_check', methods: ['GET'])]
    public function check(string $token): JsonResponse
    {
        $registro = $this->tokens->findVigentePorToken($token);

        if ($registro === null) {
            return $this->json([
                'valido' => false,
                'message' => 'El enlace de recuperación no es válido o ha caducado.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'valido' => true,
            'correoElectronico' => $this->enmascararCorreo((string) $registro->getUsuario()?->getCorreoElectronico()),
            'caducaEn' => $registro->getFechaExpiracion()?->format(\DATE_ATOM),
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/reset', name: 'api_password_reset', methods: ['POST'])]
    public function reset(Request $request): JsonResponse
    {
        $datos = $this->jsonBody($request);

        $tokenPlano = $datos['token'] ?? null;
        $contrasenya = $datos['contrasenya'] ?? $datos['password'] ?? null;
        $contrasenyaRepetida = $datos['contrasenyaRepetida'] ?? $datos['passwordConfirmation'] ?? null;

        $errores = [];

        if (!\is_string($tokenPlano) || trim($tokenPlano) === '') {
            $errores['token'][] = 'El token es obligatorio.';
        }

        if (!\is_string($contrasenya) || mb_strlen($contrasenya) < 8) {
            $errores['contrasenya'][] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if ($contrasenyaRepetida !== null && $contrasenyaRepetida !== $contrasenya) {
            $errores['contrasenyaRepetida'][] = 'Las contraseñas no coinciden.';
        }

        if ($errores !== []) {
            return $this->respuestaErrores($errores);
        }

        $registro = $this->tokens->findVigentePorToken(trim((string) $tokenPlano));

        if ($registro === null || $registro->getUsuario() === null) {
            return $this->json([
                'message' => 'El enlace de recuperación no es válido o ha caducado.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $usuario = $registro->getUsuario();
        $usuario->setContrasenya($this->passwordHasher->hashPassword($usuario, (string) $contrasenya));
        $registro->setUsado(true);
        $this->tokens->invalidarPendientesDe($usuario);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Contraseña actualizada correctamente.',
        ], JsonResponse::HTTP_OK);
    }

    private function enviarCorreoRecuperacion(string $destinatario, ?string $nombre, string $enlace): void
    {
        $minutos = (int) ceil($this->tokenTtl / 60);

        $html = $this->renderView('email/reset_password.html.twig', [
            'nombre' => $nombre,
            'enlace' => $enlace,
            'minutos' => $minutos,
        ]);

        $email = (new Email())
            ->from(new Address($this->remitente, $this->remitenteNombre))
            ->to($destinatario)
            ->subject('Recuperación de contraseña')
            ->text(sprintf(
                "Hola%s,\n\nHemos recibido una solicitud para restablecer tu contraseña.\n\nAbre este enlace para crear una nueva: %s\n\nEl enlace caduca en %d minutos. Si no has solicitado el cambio, ignora este correo.\n",
                $nombre !== null ? ' ' . $nombre : '',
                $enlace,
                $minutos
            ))
            ->html($html);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // No se debe revelar al cliente si el envío ha fallado; se registra para revisión.
            $this->logger->error('No se pudo enviar el correo de recuperación de contraseña.', [
                'destinatario' => $destinatario,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function enmascararCorreo(string $correo): string
    {
        $posicion = strpos($correo, '@');

        if ($posicion === false || $posicion === 0) {
            return $correo;
        }

        $usuario = substr($correo, 0, $posicion);
        $dominio = substr($correo, $posicion);
        $visibles = min(2, strlen($usuario));

        return substr($usuario, 0, $visibles) . str_repeat('*', max(1, strlen($usuario) - $visibles)) . $dominio;
    }
}
