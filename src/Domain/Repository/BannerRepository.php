<?php declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\BannerElement;
use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\EntityNotFoundException;

interface BannerRepository
{
    /**
     * @param string $id
     *
     * @throws EntityNotFoundException
     * @return BannerElement
     */
    public function assertFindById(string $id): BannerElement;

    /**
     * @param BannerElement $element
     */
    public function persist(BannerElement $element): void;

    /**
     * @param BannerElement|null $element
     */
    public function flush(BannerElement $element = null): void;

    /**
     * @param BannerElement $element
     */
    public function remove(BannerElement $element): void;

    /**
     * Find all banners for a selected group
     * (includes not active and expired ones)
     *
     * @param BannerGroup $group
     *
     * @return BannerElement[]
     */
    public function findAllBanners(BannerGroup $group): array;

    /**
     * Find all banners that are ready to be shown public
     *
     * @param BannerGroup $group
     * @param int $max
     *
     * @return BannerElement[]
     */
    public function findPublishedBanners(BannerGroup $group, int $max = 50): array;
}
