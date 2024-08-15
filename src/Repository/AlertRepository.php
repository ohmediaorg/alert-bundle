<?php

namespace OHMedia\AlertBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\AlertBundle\Entity\Alert;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
use OHMedia\WysiwygBundle\Repository\WysiwygRepositoryInterface;

/**
 * @method Alert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alert[]    findAll()
 * @method Alert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertRepository extends ServiceEntityRepository implements WysiwygRepositoryInterface
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
            ->orderBy('a.starts_at', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function containsWysiwygShortcodes(string ...$shortcodes): bool
    {
        $ors = [];
        $params = new ArrayCollection();

        foreach ($shortcodes as $i => $shortcode) {
            $ors[] = 'a.content LIKE :shortcode_'.$i;
            $params[] = new Parameter('shortcode_'.$i, '%'.$shortcode.'%');
        }

        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where(implode(' OR ', $ors))
            ->setParameters($params)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
