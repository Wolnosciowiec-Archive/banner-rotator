<?php declare(strict_types=1);

namespace App\Domain\Manager;

use App\Domain\Repository\BannerRepository;
use App\Domain\Repository\GroupRepository;
use App\Domain\Entity\BannerElement;
use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Exception\ManagerException;

/**
 * Manages banners
 */
class BannerManager
{
    /**
     * @var BannerRepository $repository
     */
    protected $repository;

    /**
     * @var GroupRepository $groupRepository
     */
    protected $groupRepository;

    public function __construct(
        BannerRepository $bannerElementRepository,
        GroupRepository $bannerGroupRepository
    ) {
        $this->repository      = $bannerElementRepository;
        $this->groupRepository = $bannerGroupRepository;
    }

    /**
     * Create a banner element under a selected group
     *
     * @param string $groupId
     *
     * @return BannerElement
     * @throws EntityNotFoundException
     */
    public function createForGroup(string $groupId): BannerElement
    {
        $group = $this->groupRepository->assertFindById($groupId);

        $banner = new BannerElement();
        $banner->setGroup($group);

        return $banner;
    }

    public function deleteById(string $bannerId): void
    {
        $banner = $this->repository->assertFindById($bannerId);
        
        $this->repository->remove($banner);
        $this->repository->flush($banner);
    }

    public function storeChanges(BannerElement $banner): void
    {
        if (!$banner instanceof BannerElement) {
            return;
        }

        if (!$banner->getBannerGroup() instanceof BannerGroup) {
            throw new ManagerException('Missing banner group association');
        }

        $this->repository->persist($banner);
        $this->repository->flush($banner);
    }
}
