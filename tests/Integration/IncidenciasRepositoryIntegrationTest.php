<?php

namespace App\Tests\Integration;

use App\Entity\Incidencias;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IncidenciasRepositoryIntegrationTest extends KernelTestCase
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
        // 1. Crear y persistir la entidad Incidencias
        $incidencia = new Incidencias();
        $incidencia->setIncidencia('Retraso en la recogida');
        $incidencia->setRegistroCodigo(10);

        $this->entityManager->persist($incidencia);
        $this->entityManager->flush();

        // 2. Consultar a través del repositorio de Doctrine
        $repository = $this->entityManager->getRepository(Incidencias::class);
        $result = $repository->findOneBy(['incidencia' => 'Retraso en la recogida']);

        // 3. Aserciones de integridad
        self::assertNotNull($result);
        self::assertEquals(10, $result->getRegistroCodigo());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager = null;
    }
}
