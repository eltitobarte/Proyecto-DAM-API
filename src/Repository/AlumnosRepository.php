<?php

namespace App\Repository;

use App\Entity\Alumnos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alumnos>
 */
class AlumnosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alumnos::class);
    }

    /**
     * Devuelve los alumnos a cargo de un tutor legal.
     *
     * @return Alumnos[]
     */
    public function findByTutorLegal(int $tutorLegalId, bool $soloActivos = false): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('IDENTITY(a.tutorLegalCodigo) = :tutorLegalId')
            ->setParameter('tutorLegalId', $tutorLegalId)
            ->orderBy('a.apellidos', 'ASC')
            ->addOrderBy('a.nombre', 'ASC');

        if ($soloActivos) {
            $qb->andWhere('a.activo = true');
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Alumnos[] Returns an array of Alumnos objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Alumnos
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
