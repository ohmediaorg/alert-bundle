<?php

namespace OHMedia\AlertBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\AlertBundle\Entity\Alert;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;

/**
 * @method Alert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alert[]    findAll()
 * @method Alert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    public function save(Alert $alert, bool $flush = false): void
    {
        $this->getEntityManager()->persist($alert);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Alert $alert, bool $flush = false): void
    {
        $this->getEntityManager()->remove($alert);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getActive(): ?Alert
    {
        return $this->createQueryBuilder('a')
            ->where('a.starts_at IS NOT NULL')
            ->andWhere('a.starts_at < :now')
            ->andWhere('(a.ends_at IS NULL OR a.ends_at > :now)')
            ->setParameter('now', DateTimeUtil::getDateTimeUtc())
            ->orderBy('a.starts_at', 'asc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
