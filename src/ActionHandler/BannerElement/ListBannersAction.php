<?php declare(strict_types = 1);

namespace App\ActionHandler\BannerElement;

use App\Entity\BannerGroup;
use App\Exception\EntityNotFoundException;
use App\Manager\BannerManager;
use App\Manager\GroupManager;

class ListBannersAction
{
    /**
     * @var BannerManager $manager
     */
    protected $manager;

    /**
     * @var GroupManager $groupManager
     */
    protected $groupManager;
    
    public function __construct(BannerManager $manager, GroupManager $groupManager)
    {
        $this->manager      = $manager;
        $this->groupManager = $groupManager;
    }

    /**
     * @param string $groupName
     *
     * @return \App\Entity\BannerElement[]|\Doctrine\Common\Collections\ArrayCollection
     * @throws EntityNotFoundException
     */
    public function getListOfBanners(string $groupName)
    {
        return $this->manager->getRepository()->findAllBanners($this->assertGetGroup($groupName));
    }

    /**
     * @param string $groupName
     *
     * @return BannerGroup
     * @throws EntityNotFoundException
     */
    private function assertGetGroup(string $groupName): BannerGroup
    {
        $group = $this->groupManager->getRepository()->find($groupName);

        if (!$group instanceof BannerGroup) {
            throw new EntityNotFoundException('Cannot find group "' . $groupName . '"');
        }
        
        return $group;
    }
}