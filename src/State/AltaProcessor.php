<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Altas;
use App\Repository\AlumnosRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class AltaProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private AlumnosRepository $alumnosRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Altas) {
            $alumno = $data->getAlumno();

            if ($alumno) {
                $estado = mb_strtolower(trim((string) $data->getEstado()));

                // Si el alta se tramita como aprobada/activa/completada
                if (in_array($estado, ['aprobada', 'activa', 'completada', 'aceptada'], true)) {
                    if ($alumno->isActivo()) {
                        throw new UnprocessableEntityHttpException(
                            sprintf('ALTA DUPLICADA: El alumno %s %s ya figura como activo en el centro.', $alumno->getNombre(), $alumno->getApellidos())
                        );
                    }

                    // Verificamos que el aula asignada tenga plaza libre antes de reactivarlo
                    $aula = $alumno->getAulaCodigo();
                    if ($aula) {
                        $ocupacionActual = count($this->alumnosRepository->findBy([
                            'aulaCodigo' => $aula,
                            'activo' => true,
                        ]));

                        if ($ocupacionActual >= $aula->getCapacidadMaxima()) {
                            throw new UnprocessableEntityHttpException(
                                sprintf(
                                    'SIN PLAZA: No se puede tramitar el alta porque el aula "%s" está al completo (%d/%d).',
                                    $aula->getAulaNombre(),
                                    $ocupacionActual,
                                    $aula->getCapacidadMaxima()
                                )
                            );
                        }
                    }

                    // Activamos al alumno automáticamente; Doctrine guardará ambos cambios en la misma transacción
                    $alumno->setActivo(true);
                }
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}