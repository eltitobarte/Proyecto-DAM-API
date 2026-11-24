<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractApiController extends AbstractController
{
    /**
     * Decodifica el cuerpo JSON de la petición.
     *
     * @return array<string, mixed>
     */
    protected function jsonBody(Request $request): array
    {
        $contenido = trim((string) $request->getContent());

        if ($contenido === '') {
            return [];
        }

        try {
            $datos = json_decode($contenido, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new BadRequestHttpException('El cuerpo de la petición no es un JSON válido.');
        }

        if (!\is_array($datos)) {
            throw new BadRequestHttpException('El cuerpo de la petición debe ser un objeto JSON.');
        }

        return $datos;
    }

    /**
     * Convierte una lista de violaciones de validación en un array campo => mensajes.
     *
     * @return array<string, string[]>
     */
    protected function erroresDeValidacion(ConstraintViolationListInterface $violaciones): array
    {
        $errores = [];

        foreach ($violaciones as $violacion) {
            $campo = $violacion->getPropertyPath() !== '' ? $violacion->getPropertyPath() : 'general';
            $errores[$campo][] = $violacion->getMessage();
        }

        return $errores;
    }

    /**
     * @param array<string, string[]> $errores
     */
    protected function respuestaErrores(array $errores, int $codigo = JsonResponse::HTTP_UNPROCESSABLE_ENTITY, string $mensaje = 'Datos inválidos.'): JsonResponse
    {
        return $this->json([
            'message' => $mensaje,
            'errors' => $errores,
        ], $codigo);
    }

    /**
     * Interpreta un valor booleano recibido por JSON o query string.
     */
    protected function parseBoolean(mixed $valor): ?bool
    {
        if (\is_bool($valor)) {
            return $valor;
        }

        if (\is_int($valor)) {
            return $valor !== 0;
        }

        if (\is_string($valor)) {
            return match (mb_strtolower(trim($valor))) {
                '1', 'true', 'si', 'sí', 'yes' => true,
                '0', 'false', 'no' => false,
                default => null,
            };
        }

        return null;
    }
}
