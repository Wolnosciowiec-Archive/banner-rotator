<?php declare(strict_types=1);

namespace App\Domain\ActionHandler;

use App\Domain\Repository\BannerRepository;
use App\Domain\Repository\GroupRepository;

class BrowseAction
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var BannerRepository
     */
    private $bannerRepository;

    public function __construct(GroupRepository $groupRepository, BannerRepository $bannerRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * Returns published banners for a group in format {group: <BannerGroup>, elements: <BannerElement[]>}
     *
     * @param string $groupId
     * @param int $limit
     * @param bool $randomize
     *
     * @return array
     */
    public function handle(string $groupId, int $limit = 50, bool $randomize = false): array
    {
        $bannerGroup = $this->groupRepository->assertFindById($groupId);

        $elements = $this->bannerRepository->findPublishedBanners(
            $bannerGroup,
            $limit
        );

        if ($randomize) {
            shuffle($elements);
        }

        return [
            'group'    => $bannerGroup,
            'elements' => $elements,
        ];
    }
}
