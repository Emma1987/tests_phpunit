<?php

namespace App\Repository;

use App\Entity\Octopus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Octopus>
 *
 * @method null|Octopus find($id, $lockMode = null, $lockVersion = null)
 * @method null|Octopus findOneBy(array $criteria, array $orderBy = null)
 * @method Octopus[]    findAll()
 * @method Octopus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OctopusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Octopus::class);
    }
}
