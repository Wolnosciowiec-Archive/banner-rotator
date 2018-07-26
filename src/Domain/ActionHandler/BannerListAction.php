<?php declare(strict_types=1);

namespace App\Domain\ActionHandler;

use App\Domain\Repository\BannerRepository;
use App\Domain\Entity\BannerElement;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Manager\BannerManager;
use App\Domain\Repository\GroupRepository;

class BannerListAction
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var BannerRepository
     */
    protected $bannerRepository;
    
    public function __construct(
        GroupRepository $groupRepository,
        BannerRepository $bannerRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @param string $groupName
     *
     * @return BannerElement[]
     * @throws EntityNotFoundException
     */
    public function handle(string $groupName): array
    {
        return $this->bannerRepository->findAllBanners(
            $this->groupRepository->assertFindById($groupName)
        );
    }
}
