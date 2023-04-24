<?php

namespace App\Repository;

use App\Entity\FunFact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FunFact>
 *
 * @method FunFact|null find($id, $lockMode = null, $lockVersion = null)
 * @method FunFact|null findOneBy(array $criteria, array $orderBy = null)
 * @method FunFact[]    findAll()
 * @method FunFact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FunFactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FunFact::class);
    }

    public function findAllFunFactsOrderedByFriendTypeAndContentAsc(): array
    {
        return $this->createQueryBuilder('ff')
            ->addOrderBy('ff.friendType', 'ASC')
            ->addOrderBy('ff.content', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
