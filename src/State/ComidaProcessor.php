<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Comida;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class ComidaProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Comida) {
            $alumno = $data->getAlumno();
            $menu = $data->getMenu();

            if ($alumno && $menu) {
                // Extraemos los nombres de las alergias del alumno en minúsculas
                $alergiasAlumno = [];
                foreach ($alumno->getAlergias() as $alergia) {
                    if ($alergia->getAlergia()) {
                        $alergiasAlumno[] = mb_strtolower(trim($alergia->getAlergia()));
                    }
                }

                // Comprobamos si algún alérgeno del menú coincide con las alergias del alumno
                foreach ($menu->getAlergenos() as $alergenoMenu) {
                    $nombreAlergeno = mb_strtolower(trim($alergenoMenu->getAlergeno()));
                    
                    if (in_array($nombreAlergeno, $alergiasAlumno, true)) {
                        throw new UnprocessableEntityHttpException(
                            sprintf(
                                'ALERTA DE ALERGIA: El alumno %s %s es alérgico a "%s", presente en el menú "%s".',
                                $alumno->getNombre(),
                                $alumno->getApellidos(),
                                $alergenoMenu->getAlergeno(),
                                $menu->getMenu()
                            )
                        );
                    }
                }
            }
        }

        // Si no hay conflicto, continúa y guarda en MySQL normalmente
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}