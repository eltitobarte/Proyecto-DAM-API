<?php

namespace App\Controller;

use App\Entity\Alumnos;
use App\Repository\AlumnosRepository;
use App\Repository\TutorLegalRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class TutorLegalController extends AbstractApiController
{
    public function __construct(
        private readonly TutorLegalRepository $tutores,
        private readonly AlumnosRepository $alumnos,
    ) {
    }

    #[Route('/tutor_legal/{id}/alumnos', name: 'api_tutor_legal_alumnos', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/tutor_legals/{id}/alumnos', name: 'api_tutor_legals_alumnos', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function alumnos(int $id, Request $request): JsonResponse
    {
        $tutor = $this->tutores->find($id);

        if ($tutor === null) {
            return $this->json(['message' => 'Tutor legal no encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $soloActivos = false;
        $activos = $request->query->get('activos');

        if ($activos !== null && $activos !== '') {
            $valor = $this->parseBoolean($activos);

            if ($valor === null) {
                return $this->respuestaErrores([
                    'activos' => ['El parámetro activos debe ser un valor booleano (1/0, true/false).'],
                ]);
            }

            $soloActivos = $valor;
        }

        $alumnos = $this->alumnos->findByTutorLegal($id, $soloActivos);

        return $this->json([
            'tutorLegal' => [
                'id' => $tutor->getId(),
                'nombre' => $tutor->getNombre(),
                'apellidos' => $tutor->getApellidos(),
                'correoElectronico' => $tutor->getCorreoElectronico(),
            ],
            'total' => \count($alumnos),
            'alumnos' => array_map($this->alumnoToArray(...), $alumnos),
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @return array<string, mixed>
     */
    private function alumnoToArray(Alumnos $alumno): array
    {
        $aula = $alumno->getAulaCodigo();

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
        ];
    }
}
