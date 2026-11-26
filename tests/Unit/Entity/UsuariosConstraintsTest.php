<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Administrador;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class UsuariosConstraintsTest extends TestCase
{
    public function testLasRestriccionesDeUsuariosSeAplicanALasSubclases(): void
    {
        $administrador = (new Administrador())
            ->setContrasenya('123')
            ->setCorreoElectronico('correo-invalido');

        $violaciones = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
            ->validate($administrador);

        $propiedades = [];

        foreach ($violaciones as $violacion) {
            $propiedades[] = $violacion->getPropertyPath();
        }

        self::assertContains('contrasenya', $propiedades);
        self::assertContains('correoElectronico', $propiedades);
    }
}
