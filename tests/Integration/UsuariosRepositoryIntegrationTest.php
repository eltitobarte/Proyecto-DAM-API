<?php

namespace App\Tests\Integration;

use App\Entity\Usuarios;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UsuariosRepositoryIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testPersistAndRelationalIntegrity(): void
    {
        // 1. Crear y persistir la entidad Usuarios
        $usuario = new Usuarios();
        $usuario->setRol('ROLE_ADMIN');
        $usuario->setContrasenya('12345678');
        $usuario->setCorreoElectronico('admin@guarderia.com');

        $this->entityManager->persist($usuario);
        $this->entityManager->flush();

        // 2. Consultar a través del repositorio de Doctrine
        $repository = $this->entityManager->getRepository(Usuarios::class);
        $result = $repository->findOneBy(['correoElectronico' => 'admin@guarderia.com']);

        // 3. Aserciones de integridad
        self::assertNotNull($result);
        self::assertEquals('ROLE_ADMIN', $result->getRol());
        self::assertEquals('12345678', $result->getContrasenya());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager = null;
    }
}
