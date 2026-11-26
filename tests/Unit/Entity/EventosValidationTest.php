<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Alumnos;
use App\Entity\Eventos;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

final class EventosValidationTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testEventoValidoNoGeneraErrores(): void
    {
        $evento = (new Eventos())
            ->setTitulo('Reunión de familias')
            ->setInicio(new \DateTimeImmutable('2026-12-01 10:00:00'))
            ->setFin(new \DateTimeImmutable('2026-12-01 11:00:00'))
            ->setTipo(Eventos::TIPO_REUNION);

        self::assertCount(0, $this->validator->validate($evento));
    }

    public function testLaFechaDeFinDebeSerPosteriorAlInicio(): void
    {
        $evento = (new Eventos())
            ->setTitulo('Excursión')
            ->setInicio(new \DateTimeImmutable('2026-12-01 10:00:00'))
            ->setFin(new \DateTimeImmutable('2026-12-01 09:00:00'));

        $violaciones = $this->validator->validate($evento);

        self::assertGreaterThan(0, \count($violaciones));
        self::assertSame('fin', $violaciones[0]->getPropertyPath());
    }

    public function testElTipoDebeSerValido(): void
    {
        $evento = (new Eventos())
            ->setTitulo('Aviso')
            ->setInicio(new \DateTimeImmutable('2026-12-01 10:00:00'))
            ->setFin(new \DateTimeImmutable('2026-12-01 11:00:00'))
            ->setTipo('tipo-inventado');

        $violaciones = $this->validator->validate($evento);

        self::assertGreaterThan(0, \count($violaciones));
        self::assertSame('tipo', $violaciones[0]->getPropertyPath());
    }

    public function testLaFechaDeNacimientoNoPuedeSerFutura(): void
    {
        $alumno = (new Alumnos())
            ->setNombre('Lucía')
            ->setApellidos('García Pérez')
            ->setFechaNacimiento(new \DateTime('+1 day'));

        $violaciones = $this->validator->validate($alumno);
        $propiedades = [];

        foreach ($violaciones as $violacion) {
            $propiedades[] = $violacion->getPropertyPath();
        }

        self::assertContains('fechaNacimiento', $propiedades);
    }
}
