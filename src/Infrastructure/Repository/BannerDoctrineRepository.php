<?php declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Repository\BannerRepository;
use App\Domain\Entity\BannerElement;
use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BannerDoctrineRepository extends ServiceEntityRepository implements BannerRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BannerElement::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findPublishedBanners(BannerGroup $group, int $max = 50): array
    {
        return $this->createQueryBuilder('banner')
             ->where('banner.bannerGroup = :group')->setParameter('group', $group)
             ->andWhere('banner.active = true')
             ->andWhere('(banner.expiresAt > CURRENT_TIMESTAMP() OR banner.expiresAt is null)')
             ->orderBy('banner.id', 'DESC')
             ->setMaxResults($max)
             ->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllBanners(BannerGroup $group): array
    {
        return $this->createQueryBuilder('banner')
            ->where('banner.bannerGroup = :group')->setParameter('group', $group)
            ->orderBy('banner.id', 'DESC')
            ->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function assertFindById(string $id): BannerElement
    {
        /** @var BannerElement $ent */
        $ent = $this->find($id);

        if (!$ent) {
            throw new EntityNotFoundException('Cannot find banner');
        }

        return $ent;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(BannerElement $element): void
    {
        $this->getEntityManager()->persist($element);
    }

    /**
     * {@inheritdoc}
     */
    public function flush(BannerElement $element = null): void
    {
        $this->getEntityManager()->flush($element);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(BannerElement $element): void
    {
        $this->getEntityManager()->remove($element);
    }
}
