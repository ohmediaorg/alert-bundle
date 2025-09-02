<?php

namespace OHMedia\AlertBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function getShortcodeQueryBuilder(string $shortcode): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.content LIKE :shortcode')
            ->setParameter('shortcode', '%'.$shortcode.'%');
    }

    public function getShortcodeRoute(): string
    {
        return 'alert_edit';
    }

    public function getShortcodeRouteParams(mixed $entity): array
    {
        return ['id' => $entity->getId()];
    }

    public function getShortcodeHeading(): string
    {
        return 'Alerts';
    }

    public function getShortcodeLinkText(mixed $entity): string
    {
        return sprintf(
            '%s (ID:%s)',
            (string) $entity,
            $entity->getId(),
        );
    }
}
