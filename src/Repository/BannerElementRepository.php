<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\BannerElement;
use App\Entity\BannerGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BannerElementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BannerElement::class);
    }

    /**
     * Find all banners that are ready to be shown public
     *
     * @param BannerGroup $group
     * @param int $max
     *
     * @return mixed
     */
    public function findPublishedBanners(BannerGroup $group, int $max = 50)
    {
        return $this->createQueryBuilder('banner')
             ->where('banner.bannerGroup = :group')->setParameter('group', $group)
             ->andWhere('banner.active = true')
             ->andWhere('(banner.expiresAt > CURRENT_TIMESTAMP() OR banner.expiresAt is null)')
             ->orderBy('banner.id', 'DESC')
             ->setMaxResults($max)
             ->getQuery()->getResult();
    }
}
