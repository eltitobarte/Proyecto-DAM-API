<?php

namespace App\Tests\Integration;

use App\Entity\Alumnos;
use App\Entity\Aula;
use App\Entity\Comida;
use App\Entity\RegistroDiario;
use App\Entity\TutorLegal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AlumnosRepositoryIntegrationTest extends KernelTestCase
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
        // 1. Crear y rellenar las entidades relacionadas obligatorias
        $aula = new Aula();
        $aula->setAulaNombre('Aula Cuna'); // Asegúrate de usar el método setter correcto de Aula

        $tutor = new TutorLegal();
        $tutor->setNombre('María'); // Rellena los campos obligatorios de TutorLegal
        $tutor->setApellidos('López Pérez');

        $registro = new RegistroDiario();$registro = new RegistroDiario();
        $registro->setFecha(new \DateTime()); // Asegura que RegistroDiario cumpla con la restricción NOT NULL

        $comida = new Comida();
        $comida->setFecha(new \DateTime());

        // Persistimos las relaciones primero si no tienen cascada automática
        $this->entityManager->persist($aula);
        $this->entityManager->persist($tutor);
        $this->entityManager->persist($registro);
        $this->entityManager->persist($comida);

        // 2. Crear la entidad Alumnos asignando todos sus campos y relaciones
        $alumno = new Alumnos();
        $alumno->setNombre('Carlos');
        $alumno->setApellidos('Gómez Pérez');
        $alumno->setFechaNacimiento(new \DateTime('2023-05-10'));
        $alumno->setAulaCodigo($aula);
        $alumno->setTutorLegalCodigo($tutor);
        $alumno->setTiene($registro);
        $alumno->setRecibe($comida);

        $this->entityManager->persist($alumno);
        $this->entityManager->flush();

        // 3. Consultar a través del repositorio de Doctrine
        $repository = $this->entityManager->getRepository(Alumnos::class);
        $result = $repository->findOneBy(['nombre' => 'Carlos']);

        // 4. Aserciones de persistencia e integridad referencial
        self::assertNotNull($result);
        self::assertEquals('Gómez Pérez', $result->getApellidos());
        self::assertEquals('Aula Cuna', $result->getAulaCodigo()->getAulaNombre());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager = null;
    }
}