<?php

namespace App\Tests\Unit\Entity;

use App\Entity\RecuperacionContrasenya;
use PHPUnit\Framework\TestCase;

final class RecuperacionContrasenyaTest extends TestCase
{
    public function testUnTokenSinUsarYNoCaducadoEsVigente(): void
    {
        $token = (new RecuperacionContrasenya())
            ->setFechaSolicitud(new \DateTimeImmutable('-5 minutes'))
            ->setFechaExpiracion(new \DateTimeImmutable('+55 minutes'))
            ->setUsado(false);

        self::assertTrue($token->isVigente());
    }

    public function testUnTokenCaducadoNoEsVigente(): void
    {
        $token = (new RecuperacionContrasenya())
            ->setFechaSolicitud(new \DateTimeImmutable('-2 hours'))
            ->setFechaExpiracion(new \DateTimeImmutable('-1 hour'))
            ->setUsado(false);

        self::assertFalse($token->isVigente());
    }

    public function testUnTokenUsadoNoEsVigente(): void
    {
        $token = (new RecuperacionContrasenya())
            ->setFechaSolicitud(new \DateTimeImmutable('-5 minutes'))
            ->setFechaExpiracion(new \DateTimeImmutable('+55 minutes'))
            ->setUsado(true);

        self::assertFalse($token->isVigente());
    }
}
