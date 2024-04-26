<?php

namespace App\Repository;

use App\Entity\CaseStudy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CaseStudy>
 *
 * @method CaseStudy|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaseStudy|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaseStudy[]    findAll()
 * @method CaseStudy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaseStudyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CaseStudy::class);
    }

    public function findByAuthState(bool $authenticated): array
    {
        $qb = $this->createQueryBuilder('cs')
            ->join('cs.customer', 'c');


        if (!$authenticated) {
            $qb->where('c.active = true ');
        }

        $qb->orderBy('cs.title', 'ASC');
        $query = $qb->getQuery();

        return $query->execute();
    }
}
