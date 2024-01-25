<?php

namespace App\Repository;

use App\Entity\ResultOcrSauv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Test>
 *
 * @method ResultOcrSauv|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResultOcrSauv|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResultOcrSauv[]    findAll()
 * @method ResultOcrSauv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultOcrSauvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResultOcrSauv::class);
    }

    public function add(Test $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Test $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ResultOcr[] Returns an array of Test objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Test
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

 // @return ResultOcr[] Returns an array of Test objects
  
   public function findByPageField($value): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.page = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findPages($matricule,$annee) :array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.matricule = :mat')
            ->andWhere('e.annee = :annee')
            ->setParameter('mat', $matricule)
            ->setParameter('annee', $annee)
            ->orderBy('e.id', 'ASC')
            //->setMaxResults(1)
            ->getQuery()
            ->getResult();
            //->getOneOrNullResult();
    }
}
