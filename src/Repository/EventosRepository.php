<?php

namespace App\Repository;

use App\Entity\Aula;
use App\Entity\Eventos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Eventos>
 */
class EventosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eventos::class);
    }

    /**
     * Devuelve los eventos que solapan el rango indicado.
     *
     * @return Eventos[]
     */
    public function findEnRango(
        ?\DateTimeImmutable $desde = null,
        ?\DateTimeImmutable $hasta = null,
        ?Aula $aula = null,
        ?string $tipo = null,
        ?bool $todoElDia = null
    ): array {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.inicio', 'ASC')
            ->addOrderBy('e.id', 'ASC');

        if ($desde !== null) {
            $qb->andWhere('e.fin >= :desde')->setParameter('desde', $desde);
        }

        if ($hasta !== null) {
            $qb->andWhere('e.inicio <= :hasta')->setParameter('hasta', $hasta);
        }

        if ($aula !== null) {
            $qb->andWhere('e.aula = :aula')->setParameter('aula', $aula);
        }

        if ($tipo !== null) {
            $qb->andWhere('e.tipo = :tipo')->setParameter('tipo', $tipo);
        }

        if ($todoElDia !== null) {
            $qb->andWhere('e.todoElDia = :todoElDia')->setParameter('todoElDia', $todoElDia);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Busca un evento que se solape con el rango indicado en el mismo calendario
     * (misma aula o eventos generales del centro cuando $aula es null).
     */
    public function findSolapado(
        \DateTimeImmutable $inicio,
        \DateTimeImmutable $fin,
        ?Aula $aula = null,
        ?int $excluirId = null
    ): ?Eventos {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.inicio < :fin')->setParameter('fin', $fin)
            ->andWhere('e.fin > :inicio')->setParameter('inicio', $inicio);

        if ($aula !== null) {
            $qb->andWhere('e.aula = :aula')->setParameter('aula', $aula);
        } else {
            $qb->andWhere('e.aula IS NULL');
        }

        if ($excluirId !== null) {
            $qb->andWhere('e.id != :excluirId')->setParameter('excluirId', $excluirId);
        }

        return $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
