<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Usuarios;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserPasswordHasherProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Usuarios && $data->getContrasenya()) {
            // Evita volver a hashear una contraseña que ya está encriptada en un PUT/PATCH
            if (!str_starts_with($data->getContrasenya(), '$2y$') && !str_starts_with($data->getContrasenya(), '$argon2')) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $data,
                    $data->getContrasenya()
                );
                $data->setContrasenya($hashedPassword);
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}