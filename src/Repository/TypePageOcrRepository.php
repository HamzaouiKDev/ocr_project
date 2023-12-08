<?php

namespace App\Repository;

use App\Entity\TypePageOcr;
use App\Repository\TypePageOcrRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Test>
 *
 * @method ResultOcr|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResultOcr|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResultOcr[]    findAll()
 * @method ResultOcr[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypePageOcrRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypePageOcr::class);
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
