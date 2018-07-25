<?php declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\EntityNotFoundException;

interface GroupRepository
{
    /**
     * @param string $groupId
     *
     * @throws EntityNotFoundException
     * @return BannerGroup
     */
    public function assertFindById(string $groupId): BannerGroup;

    /**
     * @return BannerGroup[]
     */
    public function findAll(): array;

    /**
     * @param BannerGroup $group
     */
    public function persist(BannerGroup $group): void;

    /**
     * @param BannerGroup|null $group
     */
    public function flush(BannerGroup $group = null): void;

    /**
     * @param BannerGroup $group
     */
    public function remove(BannerGroup $group): void;
}
