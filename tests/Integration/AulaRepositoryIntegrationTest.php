<?php

namespace App\Tests\Integration;

use App\Entity\Aula;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AulaRepositoryIntegrationTest extends KernelTestCase
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
        // 1. Crear y persistir entidades relacionadas
        $aula = new Aula();
        $aula->setAulaNombre('Matemáticas Avanzadas');
        
        $this->entityManager->persist($aula);
        $this->entityManager->flush();

        // 2. Validar persistencia y consultas de Doctrine ORM
        $repository = $this->entityManager->getRepository(Aula::class);
        $result = $repository->findOneBy(['aulaNombre' => 'Matemáticas Avanzadas']);

        // 3. Aserciones de integridad
        self::assertNotNull($result);
        self::assertEquals('Matemáticas Avanzadas', $result->getAulaNombre());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager = null;
    }
}
