<?php

namespace App\Controller;

use App\Entity\Profesor;
use App\Entity\Telefonos;
use App\Entity\Usuarios;
use App\Repository\AulaRepository;
use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/usuarios')]
final class UsuarioController extends AbstractApiController
{
    public function __construct(
        private readonly UsuariosRepository $usuarios,
        private readonly AulaRepository $aulas,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/{id}', name: 'api_usuarios_get', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $usuario = $this->usuarios->find($id);

        if ($usuario === null) {
            return $this->json(['message' => 'Usuario no encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($this->usuarioToArray($usuario), JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_usuarios_patch', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $usuario = $this->usuarios->find($id);

        if ($usuario === null) {
            return $this->json(['message' => 'Usuario no encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $datos = $this->jsonBody($request);
        $errores = [];
        $cambios = false;

        // --- Correo electrónico ---
        if (array_key_exists('correoElectronico', $datos) || array_key_exists('email', $datos)) {
            $correo = $datos['correoElectronico'] ?? $datos['email'];

            if (!\is_string($correo) || filter_var(trim($correo), \FILTER_VALIDATE_EMAIL) === false) {
                $errores['correoElectronico'][] = 'El correo electrónico no es válido.';
            } else {
                $correo = trim($correo);
                $existente = $this->usuarios->findOneByCorreoElectronico($correo);

                if ($existente !== null && $existente->getId() !== $usuario->getId()) {
                    return $this->json([
                        'message' => 'El correo electrónico ya está registrado por otro usuario.',
                    ], JsonResponse::HTTP_CONFLICT);
                }

                $usuario->setCorreoElectronico($correo);
                $cambios = true;
            }
        }

        // --- Contraseña ---
        if (array_key_exists('contrasenya', $datos) || array_key_exists('password', $datos)) {
            $contrasenya = $datos['contrasenya'] ?? $datos['password'];

            if (!\is_string($contrasenya) || mb_strlen($contrasenya) < 8) {
                $errores['contrasenya'][] = 'La contraseña debe tener al menos 8 caracteres.';
            } else {
                $usuario->setContrasenya($this->passwordHasher->hashPassword($usuario, $contrasenya));
                $cambios = true;
            }
        }

        // --- Nombre y apellidos ---
        foreach (['nombre', 'apellidos'] as $campo) {
            if (!array_key_exists($campo, $datos)) {
                continue;
            }

            $valor = $datos[$campo];

            if (!\is_string($valor) || trim($valor) === '') {
                $errores[$campo][] = sprintf('El campo %s no puede estar vacío.', $campo);
                continue;
            }

            $valor = trim($valor);

            if (mb_strlen($valor) > 255) {
                $errores[$campo][] = sprintf('El campo %s no puede superar los 255 caracteres.', $campo);
                continue;
            }

            if (!method_exists($usuario, 'set' . ucfirst($campo))) {
                $errores[$campo][] = sprintf('Este tipo de usuario no admite el campo %s.', $campo);
                continue;
            }

            $usuario->{'set' . ucfirst($campo)}($valor);
            $cambios = true;
        }

        // --- Teléfonos ---
        if (array_key_exists('telefonos', $datos)) {
            if (!\is_array($datos['telefonos'])) {
                $errores['telefonos'][] = 'Los teléfonos deben enviarse como una lista de números.';
            } else {
                $numeros = [];

                foreach ($datos['telefonos'] as $indice => $valor) {
                    if (!\is_string($valor) || preg_match('/^[0-9+\s().-]{6,24}$/', trim($valor)) !== 1) {
                        $errores['telefonos'][] = sprintf('El teléfono en la posición %s no es válido.', (string) $indice);
                        continue;
                    }

                    $numeros[] = trim($valor);
                }

                if (!isset($errores['telefonos'])) {
                    $coleccion = $usuario->getTelefonos();

                    foreach ($coleccion->toArray() as $telefono) {
                        $coleccion->removeElement($telefono);
                    }

                    foreach ($numeros as $numero) {
                        $usuario->addTelefono((new Telefonos())->setTelefono($numero));
                    }

                    $cambios = true;
                }
            }
        }

        // --- Aula (solo profesores) ---
        if (array_key_exists('aulaCodigo', $datos) || array_key_exists('aula', $datos)) {
            $aulaId = $datos['aulaCodigo'] ?? $datos['aula'];

            if ($aulaId === null) {
                if ($usuario instanceof Profesor) {
                    $errores['aulaCodigo'][] = 'El aula de un profesor no puede ser nula.';
                }
            } elseif (!\is_int($aulaId) && !(\is_string($aulaId) && ctype_digit($aulaId))) {
                $errores['aulaCodigo'][] = 'El identificador del aula no es válido.';
            } elseif (!$usuario instanceof Profesor) {
                $errores['aulaCodigo'][] = 'Solo los profesores pueden tener un aula asignada.';
            } else {
                $aula = $this->aulas->find((int) $aulaId);

                if ($aula === null) {
                    $errores['aulaCodigo'][] = 'El aula indicada no existe.';
                } else {
                    $usuario->setImparte($aula);
                    $cambios = true;
                }
            }
        }

        if ($errores === [] && !$cambios) {
            $errores['general'][] = 'No se ha enviado ningún campo actualizable.';
        }

        $violaciones = $this->validator->validate($usuario);

        if (count($violaciones) > 0) {
            $errores = array_merge_recursive($errores, $this->erroresDeValidacion($violaciones));
        }

        if ($errores !== []) {
            return $this->respuestaErrores($errores);
        }

        $this->entityManager->flush();

        return $this->json($this->usuarioToArray($usuario), JsonResponse::HTTP_OK);
    }

    /**
     * @return array<string, mixed>
     */
    private function usuarioToArray(Usuarios $usuario): array
    {
        $aula = $usuario instanceof Profesor ? $usuario->getImparte() : null;

        return [
            'id' => $usuario->getId(),
            'correoElectronico' => $usuario->getCorreoElectronico(),
            'nombre' => method_exists($usuario, 'getNombre') ? $usuario->getNombre() : null,
            'apellidos' => method_exists($usuario, 'getApellidos') ? $usuario->getApellidos() : null,
            'roles' => $usuario->getRoles(),
            'telefonos' => array_map(
                static fn (Telefonos $telefono): array => [
                    'id' => $telefono->getId(),
                    'telefono' => $telefono->getTelefono(),
                ],
                $usuario->getTelefonos()->toArray()
            ),
            'aula' => $aula !== null ? [
                'id' => $aula->getId(),
                'aulaNombre' => $aula->getAulaNombre(),
            ] : null,
        ];
    }
}
