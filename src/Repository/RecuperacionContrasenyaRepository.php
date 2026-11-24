<?php

namespace App\Repository;

use App\Entity\RecuperacionContrasenya;
use App\Entity\Usuarios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecuperacionContrasenya>
 */
class RecuperacionContrasenyaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecuperacionContrasenya::class);
    }

    public function findVigentePorToken(string $tokenPlano): ?RecuperacionContrasenya
    {
        $registro = $this->findOneBy([
            'tokenHash' => hash('sha256', $tokenPlano),
        ]);

        return $registro !== null && $registro->isVigente() ? $registro : null;
    }

    public function invalidarPendientesDe(Usuarios $usuario): void
    {
        $this->createQueryBuilder('r')
            ->update()
            ->set('r.usado', ':usado')
            ->where('r.usuario = :usuario')
            ->andWhere('r.usado = false')
            ->setParameter('usado', true)
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->execute();
    }
}
