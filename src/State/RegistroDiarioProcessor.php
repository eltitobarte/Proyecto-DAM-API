<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\RegistroDiario;
use App\Repository\RegistroDiarioRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class RegistroDiarioProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private RegistroDiarioRepository $registroRepo
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof RegistroDiario) {
            $alumno = $data->getAlumno();
            $fecha = $data->getFecha();

            if ($alumno && !$alumno->isActivo()) {
                throw new UnprocessableEntityHttpException(
                    sprintf('No se puede crear un registro diario para %s %s porque figura como inactivo (baja).', $alumno->getNombre(), $alumno->getApellidos())
                );
            }

            if ($alumno && $fecha) {
                // Buscamos si ya existe un registro para este alumno en esa fecha exacta
                $registroExistente = $this->registroRepo->findOneBy([
                    'alumno' => $alumno,
                    'fecha' => $fecha,
                ]);

                // Si existe y no es el mismo registro que estamos editando (PUT/PATCH), bloqueamos
                if ($registroExistente && $registroExistente->getId() !== $data->getId()) {
                    throw new UnprocessableEntityHttpException(
                        sprintf(
                            'REGISTRO DUPLICADO: El alumno %s %s ya tiene un registro diario creado para el %s (ID #%d). Edita el existente en lugar de crear uno nuevo.',
                            $alumno->getNombre(),
                            $alumno->getApellidos(),
                            $fecha->format('d/m/Y'),
                            $registroExistente->getId()
                        )
                    );
                }
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}