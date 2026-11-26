<?php

namespace App\Controller;

use App\Entity\Eventos;
use App\Repository\AulaRepository;
use App\Repository\EventosRepository;
use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/eventos')]
final class EventoController extends AbstractApiController
{
    public function __construct(
        private readonly EventosRepository $eventos,
        private readonly AulaRepository $aulas,
        private readonly UsuariosRepository $usuarios,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', name: 'api_eventos_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $errores = [];

        $desde = $this->parseFecha($request->query->get('desde'), false, $errores, 'desde');
        $hasta = $this->parseFecha($request->query->get('hasta'), true, $errores, 'hasta');

        if ($desde !== null && $hasta !== null && $desde > $hasta) {
            $errores['desde'][] = 'La fecha "desde" no puede ser posterior a la fecha "hasta".';
        }

        $aula = null;
        $aulaId = $request->query->get('aula');

        if ($aulaId !== null && $aulaId !== '') {
            if (!ctype_digit((string) $aulaId)) {
                $errores['aula'][] = 'El identificador del aula no es válido.';
            } else {
                $aula = $this->aulas->find((int) $aulaId);

                if ($aula === null) {
                    $errores['aula'][] = 'El aula indicada no existe.';
                }
            }
        }

        $tipo = $request->query->get('tipo');

        if ($tipo !== null && !\in_array($tipo, Eventos::TIPOS, true)) {
            $errores['tipo'][] = sprintf('El tipo debe ser uno de: %s.', implode(', ', Eventos::TIPOS));
        }

        $todoElDia = null;
        $todoElDiaParam = $request->query->get('todoElDia');

        if ($todoElDiaParam !== null && $todoElDiaParam !== '') {
            $todoElDia = $this->parseBoolean($todoElDiaParam);

            if ($todoElDia === null) {
                $errores['todoElDia'][] = 'El parámetro todoElDia debe ser un valor booleano.';
            }
        }

        if ($errores !== []) {
            return $this->respuestaErrores($errores);
        }

        $eventos = $this->eventos->findEnRango($desde, $hasta, $aula, $tipo, $todoElDia);

        return $this->json([
            'total' => \count($eventos),
            'eventos' => array_map($this->eventoToArray(...), $eventos),
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_eventos_get', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $evento = $this->eventos->find($id);

        if ($evento === null) {
            return $this->json(['message' => 'Evento no encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($this->eventoToArray($evento), JsonResponse::HTTP_OK);
    }

    #[Route('', name: 'api_eventos_create', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $datos = $this->jsonBody($request);
        $errores = [];
        $evento = new Eventos();

        $this->aplicarDatos($evento, $datos, false, $errores);

        if ($errores === []) {
            $this->normalizarTodoElDia($evento);
            $this->validarEvento($evento, $errores);
        }

        if ($errores === []) {
            $conflicto = $this->buscarConflicto($evento);

            if ($conflicto !== null) {
                return $this->respuestaConflicto($conflicto);
            }
        }

        if ($errores !== []) {
            return $this->respuestaErrores($errores);
        }

        $this->entityManager->persist($evento);
        $this->entityManager->flush();

        return $this->json(
            $this->eventoToArray($evento),
            JsonResponse::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_eventos_get', ['id' => $evento->getId()])]
        );
    }

    #[Route('/{id}', name: 'api_eventos_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        return $this->actualizar($id, $request, false);
    }

    #[Route('/{id}', name: 'api_eventos_patch', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function patch(int $id, Request $request): JsonResponse
    {
        return $this->actualizar($id, $request, true);
    }

    #[Route('/{id}', name: 'api_eventos_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function destroy(int $id): Response
    {
        $evento = $this->eventos->find($id);

        if ($evento === null) {
            return $this->json(['message' => 'Evento no encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($evento);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function actualizar(int $id, Request $request, bool $parcial): JsonResponse
    {
        $evento = $this->eventos->find($id);

        if ($evento === null) {
            return $this->json(['message' => 'Evento no encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $datos = $this->jsonBody($request);
        $errores = [];

        $this->aplicarDatos($evento, $datos, $parcial, $errores);

        if ($errores === []) {
            $this->normalizarTodoElDia($evento);
            $this->validarEvento($evento, $errores);
        }

        if ($errores === []) {
            $conflicto = $this->buscarConflicto($evento);

            if ($conflicto !== null) {
                return $this->respuestaConflicto($conflicto);
            }
        }

        if ($errores !== []) {
            return $this->respuestaErrores($errores);
        }

        $this->entityManager->flush();

        return $this->json($this->eventoToArray($evento), JsonResponse::HTTP_OK);
    }

    /**
     * @param array<string, mixed> $datos
     * @param array<string, string[]> $errores
     */
    private function aplicarDatos(Eventos $evento, array $datos, bool $parcial, array &$errores): void
    {
        if (!$parcial || array_key_exists('titulo', $datos)) {
            $titulo = $datos['titulo'] ?? null;

            if (!\is_string($titulo) || trim($titulo) === '') {
                $errores['titulo'][] = 'El título del evento es obligatorio.';
            } elseif (mb_strlen(trim($titulo)) > 255) {
                $errores['titulo'][] = 'El título no puede superar los 255 caracteres.';
            } else {
                $evento->setTitulo(trim($titulo));
            }
        }

        if (!$parcial || array_key_exists('descripcion', $datos)) {
            $descripcion = $datos['descripcion'] ?? null;

            if ($descripcion !== null && !\is_string($descripcion)) {
                $errores['descripcion'][] = 'La descripción debe ser un texto.';
            } else {
                $evento->setDescripcion($descripcion !== null ? trim($descripcion) : null);
            }
        }

        if (!$parcial || array_key_exists('lugar', $datos)) {
            $lugar = $datos['lugar'] ?? null;

            if ($lugar !== null && (!\is_string($lugar) || mb_strlen(trim($lugar)) > 255)) {
                $errores['lugar'][] = 'El lugar no es válido (máximo 255 caracteres).';
            } else {
                $evento->setLugar($lugar !== null ? trim($lugar) : null);
            }
        }

        if (!$parcial || array_key_exists('inicio', $datos)) {
            $inicio = $this->parseFechaObligatoria($datos['inicio'] ?? null, $errores, 'inicio');

            if ($inicio !== null) {
                $evento->setInicio($inicio);
            }
        }

        if (!$parcial || array_key_exists('fin', $datos)) {
            $fin = $this->parseFechaObligatoria($datos['fin'] ?? null, $errores, 'fin');

            if ($fin !== null) {
                $evento->setFin($fin);
            }
        }

        if (array_key_exists('todoElDia', $datos)) {
            $todoElDia = $this->parseBoolean($datos['todoElDia']);

            if ($todoElDia === null) {
                $errores['todoElDia'][] = 'El campo todoElDia debe ser un valor booleano.';
            } else {
                $evento->setTodoElDia($todoElDia);
            }
        }

        if (!$parcial || array_key_exists('tipo', $datos)) {
            $tipo = $datos['tipo'] ?? Eventos::TIPO_GENERAL;

            if (!\is_string($tipo) || !\in_array($tipo, Eventos::TIPOS, true)) {
                $errores['tipo'][] = sprintf('El tipo debe ser uno de: %s.', implode(', ', Eventos::TIPOS));
            } else {
                $evento->setTipo($tipo);
            }
        }

        if (array_key_exists('aulaCodigo', $datos) || array_key_exists('aula', $datos)) {
            $aulaId = $datos['aulaCodigo'] ?? $datos['aula'];

            if ($aulaId === null) {
                $evento->setAula(null);
            } elseif (!\is_int($aulaId) && !(\is_string($aulaId) && ctype_digit($aulaId))) {
                $errores['aulaCodigo'][] = 'El identificador del aula no es válido.';
            } else {
                $aula = $this->aulas->find((int) $aulaId);

                if ($aula === null) {
                    $errores['aulaCodigo'][] = 'El aula indicada no existe.';
                } else {
                    $evento->setAula($aula);
                }
            }
        }

        if (array_key_exists('creadorCodigo', $datos) || array_key_exists('creador', $datos)) {
            $creadorId = $datos['creadorCodigo'] ?? $datos['creador'];

            if ($creadorId === null) {
                $evento->setCreador(null);
            } elseif (!\is_int($creadorId) && !(\is_string($creadorId) && ctype_digit($creadorId))) {
                $errores['creadorCodigo'][] = 'El identificador del creador no es válido.';
            } else {
                $creador = $this->usuarios->find((int) $creadorId);

                if ($creador === null) {
                    $errores['creadorCodigo'][] = 'El usuario creador indicado no existe.';
                } else {
                    $evento->setCreador($creador);
                }
            }
        }
    }

    private function normalizarTodoElDia(Eventos $evento): void
    {
        if (!$evento->isTodoElDia()) {
            return;
        }

        $inicio = $evento->getInicio();
        $fin = $evento->getFin();

        if ($inicio === null || $fin === null) {
            return;
        }

        $evento->setInicio($inicio->setTime(0, 0, 0));
        $evento->setFin($fin->setTime(23, 59, 59));
    }

    /**
     * @param array<string, string[]> $errores
     */
    private function validarEvento(Eventos $evento, array &$errores): void
    {
        $violaciones = $this->validator->validate($evento);

        if (count($violaciones) > 0) {
            $errores = array_merge_recursive($errores, $this->erroresDeValidacion($violaciones));
            return;
        }

        if ($evento->getInicio() !== null && $evento->getFin() !== null && $evento->getFin() <= $evento->getInicio()) {
            $errores['fin'][] = 'La fecha de fin debe ser posterior a la fecha de inicio.';
        }
    }

    private function buscarConflicto(Eventos $evento): ?Eventos
    {
        if ($evento->getInicio() === null || $evento->getFin() === null) {
            return null;
        }

        return $this->eventos->findSolapado(
            $evento->getInicio(),
            $evento->getFin(),
            $evento->getAula(),
            $evento->getId()
        );
    }

    private function respuestaConflicto(Eventos $conflicto): JsonResponse
    {
        return $this->json([
            'message' => 'Ya existe otro evento que se solapa en el mismo calendario.',
            'evento' => $this->eventoToArray($conflicto),
        ], JsonResponse::HTTP_CONFLICT);
    }

    /**
     * @param array<string, string[]> $errores
     */
    private function parseFechaObligatoria(mixed $valor, array &$errores, string $campo): ?\DateTimeImmutable
    {
        if (!\is_string($valor) || trim($valor) === '') {
            $errores[$campo][] = 'La fecha es obligatoria. Usa el formato YYYY-MM-DD HH:MM:SS o ISO 8601.';
            return null;
        }

        try {
            return new \DateTimeImmutable(trim($valor));
        } catch (\Exception) {
            $errores[$campo][] = 'La fecha no es válida. Usa el formato YYYY-MM-DD HH:MM:SS o ISO 8601.';
            return null;
        }
    }

    /**
     * @param array<string, string[]> $errores
     */
    private function parseFecha(mixed $valor, bool $finDelDia, array &$errores, string $campo): ?\DateTimeImmutable
    {
        if (!\is_string($valor) || trim($valor) === '') {
            return null;
        }

        $texto = trim($valor);

        try {
            $fecha = new \DateTimeImmutable($texto);
        } catch (\Exception) {
            $errores[$campo][] = 'La fecha no es válida. Usa el formato YYYY-MM-DD o ISO 8601.';
            return null;
        }

        if ($finDelDia && preg_match('/^\d{4}-\d{2}-\d{2}$/', $texto) === 1) {
            $fecha = $fecha->setTime(23, 59, 59);
        }

        return $fecha;
    }

    /**
     * @return array<string, mixed>
     */
    private function eventoToArray(Eventos $evento): array
    {
        $aula = $evento->getAula();
        $creador = $evento->getCreador();

        return [
            'id' => $evento->getId(),
            'titulo' => $evento->getTitulo(),
            'descripcion' => $evento->getDescripcion(),
            'inicio' => $evento->getInicio()?->format(\DATE_ATOM),
            'fin' => $evento->getFin()?->format(\DATE_ATOM),
            'todoElDia' => $evento->isTodoElDia(),
            'tipo' => $evento->getTipo(),
            'lugar' => $evento->getLugar(),
            'aula' => $aula !== null ? [
                'id' => $aula->getId(),
                'aulaNombre' => $aula->getAulaNombre(),
            ] : null,
            'creador' => $creador !== null ? [
                'id' => $creador->getId(),
                'correoElectronico' => $creador->getCorreoElectronico(),
                'nombre' => method_exists($creador, 'getNombre') ? $creador->getNombre() : null,
                'apellidos' => method_exists($creador, 'getApellidos') ? $creador->getApellidos() : null,
            ] : null,
            'fechaCreacion' => $evento->getFechaCreacion()?->format(\DATE_ATOM),
        ];
    }
}