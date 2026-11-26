<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Bajas;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class BajaProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Bajas) {
            $alumno = $data->getAlumno();

            if ($alumno) {
                $estado = mb_strtolower(trim((string) $data->getEstado()));

                // Si la baja se tramita como aprobada/efectiva/completada
                if (in_array($estado, ['aprobada', 'efectiva', 'completada', 'tramitada'], true)) {
                    if (!$alumno->isActivo()) {
                        throw new UnprocessableEntityHttpException(
                            sprintf('BAJA DUPLICADA: El alumno %s %s ya figuraba como inactivo (dado de baja).', $alumno->getNombre(), $alumno->getApellidos())
                        );
                    }

                    // Desactivamos al alumno: conserva su historial pero libera inmediatamente su plaza en el aula
                    $alumno->setActivo(false);
                }
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}