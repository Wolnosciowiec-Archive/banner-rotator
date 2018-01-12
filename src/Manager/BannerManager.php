<?php declare(strict_types = 1);

namespace App\Manager;

use App\Repository\BannerElementRepository;
use App\Repository\BannerGroupRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Manages banners
 */
class BannerManager
{
    /**
     * @var BannerGroupRepository $groupRepository
     */
    protected $groupRepository;

    /**
     * @var BannerElementRepository $elementRepository
     */
    protected $elementRepository;

    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    public function __construct(
        BannerGroupRepository $bannerGroupRepository,
        BannerElementRepository $bannerElementRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->groupRepository   = $bannerGroupRepository;
        $this->elementRepository = $bannerElementRepository;
        $this->em                = $entityManager;
    }

    /**
     * @return BannerGroupRepository
     */
    public function getGroupRepository(): BannerGroupRepository
    {
        return $this->groupRepository;
    }

    /**
     * @return BannerElementRepository
     */
    public function getElementRepository(): BannerElementRepository
    {
        return $this->elementRepository;
    }
}
