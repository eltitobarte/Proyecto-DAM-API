<?php

namespace App\Controller;

use App\Entity\Alumnos;
use App\Entity\Aula;
use App\Repository\AlumnosRepository;
use App\Repository\AulaRepository;
use App\Repository\TutorLegalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/alumnos')]
final class AlumnoController extends AbstractApiController
{
    private const CAMPOS_ACTUALIZABLES = [
        'nombre',
        'apellidos',
        'fechaNacimiento',
        'aulaCodigo',
        'aula',
        'tutorLegalCodigo',
        'tutorLegal',
        'activo',
    ];

    public function __construct(
        private readonly AlumnosRepository $alumnos,
        private readonly AulaRepository $aulas,
        private readonly TutorLegalRepository $tutores,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/{id}', name: 'api_alumnos_patch', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $alumno = $this->alumnos->find($id);

        if ($alumno === null) {
            return $this->json(['message' => 'Alumno no encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $datos = $this->jsonBody($request);

        if (array_intersect(array_keys($datos), self::CAMPOS_ACTUALIZABLES) === []) {
            return $this->respuestaErrores([
                'general' => ['No se ha enviado ningún campo actualizable.'],
            ]);
        }

        $errores = [];

        // Estado actual: se necesita para calcular la ocupación del aula antes de aplicar cambios.
        $aulaOriginal = $alumno->getAulaCodigo();
        $activoOriginal = $alumno->isActivo();

        $nuevoNombre = null;
        $nuevosApellidos = null;
        $nuevaFechaNacimiento = null;
        $nuevoActivo = null;
        $nuevaAula = null;
        $nuevoTutor = null;

        foreach (['nombre', 'apellidos'] as $campo) {
            if (!array_key_exists($campo, $datos)) {
                continue;
            }

            $valor = $datos[$campo];

            if (!\is_string($valor) || trim($valor) === '') {
                $errores[$campo][] = sprintf('El campo %s del alumno no puede estar vacío.', $campo);
                continue;
            }

            if (mb_strlen(trim($valor)) > 255) {
                $errores[$campo][] = sprintf('El campo %s no puede superar los 255 caracteres.', $campo);
                continue;
            }

            if ($campo === 'nombre') {
                $nuevoNombre = trim($valor);
            } else {
                $nuevosApellidos = trim($valor);
            }
        }

        if (array_key_exists('fechaNacimiento', $datos)) {
            $fecha = $this->parseFechaNacimiento($datos['fechaNacimiento']);

            if ($fecha === null) {
                $errores['fechaNacimiento'][] = 'La fecha de nacimiento no es válida. Usa el formato YYYY-MM-DD.';
            } elseif ($fecha > new \DateTime('today')) {
                $errores['fechaNacimiento'][] = 'La fecha de nacimiento no puede ser futura.';
            } else {
                $nuevaFechaNacimiento = $fecha;
            }
        }

        if (array_key_exists('activo', $datos)) {
            $activo = $this->parseBoolean($datos['activo']);

            if ($activo === null) {
                $errores['activo'][] = 'El campo activo debe ser un valor booleano.';
            } else {
                $nuevoActivo = $activo;
            }
        }

        if (array_key_exists('aulaCodigo', $datos) || array_key_exists('aula', $datos)) {
            $nuevaAula = $this->resolverAula($datos['aulaCodigo'] ?? $datos['aula'], $errores);
        }

        if (array_key_exists('tutorLegalCodigo', $datos) || array_key_exists('tutorLegal', $datos)) {
            $tutorId = $datos['tutorLegalCodigo'] ?? $datos['tutorLegal'];
            $tutor = \is_int($tutorId) || (\is_string($tutorId) && ctype_digit($tutorId))
                ? $this->tutores->find((int) $tutorId)
                : null;

            if ($tutor === null) {
                $errores['tutorLegalCodigo'][] = 'El tutor legal indicado no existe.';
            } else {
                $nuevoTutor = $tutor;
            }
        }

        $aulaDestino = $nuevaAula ?? $aulaOriginal;
        $activoDestino = $nuevoActivo ?? $activoOriginal;

        if ($errores === [] && $activoDestino && $aulaDestino !== null) {
            $ocupacion = \count($this->alumnos->findBy([
                'aulaCodigo' => $aulaDestino,
                'activo' => true,
            ]));

            if ($aulaOriginal !== null && $aulaOriginal->getId() === $aulaDestino->getId() && $activoOriginal) {
                --$ocupacion;
            }

            $capacidad = $aulaDestino->getCapacidadMaxima();

            if ($capacidad !== null && $ocupacion >= $capacidad) {
                $errores['aulaCodigo'][] = sprintf(
                    'AFORO COMPLETO: El aula "%s" ha alcanzado su capacidad máxima (%d/%d alumnos activos).',
                    $aulaDestino->getAulaNombre(),
                    $ocupacion,
                    $capacidad
                );
            }
        }

        if ($errores !== []) {
            return $this->respuestaErrores($errores);
        }

        if ($nuevoNombre !== null) {
            $alumno->setNombre($nuevoNombre);
        }

        if ($nuevosApellidos !== null) {
            $alumno->setApellidos($nuevosApellidos);
        }

        if ($nuevaFechaNacimiento !== null) {
            $alumno->setFechaNacimiento($nuevaFechaNacimiento);
        }

        if ($nuevoActivo !== null) {
            $alumno->setActivo($nuevoActivo);
        }

        if ($nuevaAula !== null) {
            $alumno->setAulaCodigo($nuevaAula);
        }

        if ($nuevoTutor !== null) {
            $alumno->setTutorLegalCodigo($nuevoTutor);
        }

        $violaciones = $this->validator->validate($alumno);

        if (count($violaciones) > 0) {
            return $this->respuestaErrores($this->erroresDeValidacion($violaciones));
        }

        $this->entityManager->flush();

        return $this->json($this->alumnoToArray($alumno), JsonResponse::HTTP_OK);
    }

    /**
     * @param array<string, string[]> $errores
     */
    private function resolverAula(mixed $valor, array &$errores): ?Aula
    {
        if (!\is_int($valor) && !(\is_string($valor) && ctype_digit($valor))) {
            $errores['aulaCodigo'][] = 'El identificador del aula no es válido.';
            return null;
        }

        $aula = $this->aulas->find((int) $valor);

        if ($aula === null) {
            $errores['aulaCodigo'][] = 'El aula indicada no existe.';
        }

        return $aula;
    }

    private function parseFechaNacimiento(mixed $valor): ?\DateTime
    {
        if (!\is_string($valor)) {
            return null;
        }

        $texto = trim($valor);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $texto) === 1) {
            $fecha = \DateTime::createFromFormat('!Y-m-d', $texto);
            $erroresPhp = \DateTime::getLastErrors();

            if ($fecha === false || ($erroresPhp !== false && ($erroresPhp['warning_count'] > 0 || $erroresPhp['error_count'] > 0))) {
                return null;
            }

            return $fecha;
        }

        // Se admite también una fecha ISO 8601 completa (por ejemplo la que envía Android).
        if (preg_match('/^\d{4}-\d{2}-\d{2}[T ].+$/', $texto) !== 1) {
            return null;
        }

        try {
            $fecha = new \DateTime($texto);
        } catch (\Exception) {
            return null;
        }

        return $fecha->setTime(0, 0, 0);
    }

    /**
     * @return array<string, mixed>
     */
    private function alumnoToArray(Alumnos $alumno): array
    {
        $aula = $alumno->getAulaCodigo();
        $tutor = $alumno->getTutorLegalCodigo();

        return [
            'id' => $alumno->getId(),
            'nombre' => $alumno->getNombre(),
            'apellidos' => $alumno->getApellidos(),
            'fechaNacimiento' => $alumno->getFechaNacimiento()?->format('Y-m-d'),
            'activo' => $alumno->isActivo(),
            'aula' => $aula !== null ? [
                'id' => $aula->getId(),
                'aulaNombre' => $aula->getAulaNombre(),
            ] : null,
            'tutorLegal' => $tutor !== null ? [
                'id' => $tutor->getId(),
                'nombre' => $tutor->getNombre(),
                'apellidos' => $tutor->getApellidos(),
                'correoElectronico' => $tutor->getCorreoElectronico(),
            ] : null,
        ];
    }
}