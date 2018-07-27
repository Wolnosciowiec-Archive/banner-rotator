<?php declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Repository\GroupRepository;
use App\Domain\Entity\BannerGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GroupDoctrineRepository extends ServiceEntityRepository implements GroupRepository
{
    /**
     * @codeCoverageIgnore
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BannerGroup::class);
    }

    /**
     * @codeCoverageIgnore
     *
     * {@inheritdoc}
     *
     * @return null|BannerGroup|object
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * @codeCoverageIgnore
     *
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

    /**
     * @codeCoverageIgnore
     *
     * {@inheritdoc}
     */
    public function assertFindById(string $groupId): BannerGroup
    {
        /** @var BannerGroup $ent */
        $ent = $this->find($groupId);

        if (!$ent) {
            throw new EntityNotFoundException('Cannot find a group by id');
        }

        return $ent;
    }

    /**
     * @codeCoverageIgnore
     *
     * {@inheritdoc}
     */
    public function persist(BannerGroup $group): void
    {
        $this->getEntityManager()->persist($group);
    }

    /**
     * @codeCoverageIgnore
     *
     * {@inheritdoc}
     */
    public function flush(BannerGroup $group = null): void
    {
        $this->getEntityManager()->flush($group);
    }

    /**
     * @codeCoverageIgnore
     *
     * {@inheritdoc}
     */
    public function remove(BannerGroup $group): void
    {
        $this->getEntityManager()->remove($group);
    }
}
