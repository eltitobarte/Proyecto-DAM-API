<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Alumnos;
use App\Repository\AlumnosRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class AlumnoProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private AlumnosRepository $alumnosRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Alumnos && $data->isActivo()) {
            $aula = $data->getAulaCodigo();

            if ($aula) {
                // Contamos cuántos alumnos activos tiene actualmente ese aula en MySQL
                $alumnosActivosEnAula = $this->alumnosRepository->findBy([
                    'aulaCodigo' => $aula,
                    'activo' => true,
                ]);

                $ocupacionActual = count($alumnosActivosEnAula);

                // Si estamos editando (PUT/PATCH) un alumno que YA pertenecía a este aula, no lo contamos doble
                $yaEstabaEnEsteAula = false;
                if ($data->getId() !== null) {
                    foreach ($alumnosActivosEnAula as $existente) {
                        if ($existente->getId() === $data->getId()) {
                            $yaEstabaEnEsteAula = true;
                            break;
                        }
                    }
                }

                if (!$yaEstabaEnEsteAula && $ocupacionActual >= $aula->getCapacidadMaxima()) {
                    throw new UnprocessableEntityHttpException(
                        sprintf(
                            'AFORO COMPLETO: El aula "%s" ha alcanzado su capacidad máxima (%d/%d alumnos activos).',
                            $aula->getAulaNombre(),
                            $ocupacionActual,
                            $aula->getCapacidadMaxima()
                        )
                    );
                }
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}