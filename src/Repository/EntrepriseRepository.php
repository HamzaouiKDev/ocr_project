<?php
namespace App\Repository;

use App\Entity\Entreprise;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Example|null find($id, $lockMode = null, $lockVersion = null)
 * @method Example|null findOneBy(array $criteria, array $orderBy = null)
 * @method Example[]    findAll()
 * @method Example[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }

    public function findFirstInvalid()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.valide = :val')
            ->setParameter('val', false)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}