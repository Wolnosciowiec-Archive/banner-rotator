<?php declare(strict_types=1);

namespace App\Domain\ActionHandler;

use App\Domain\Entity\BannerGroup;
use App\Domain\Form\GroupForm;
use App\Domain\Repository\GroupRepository;
use App\Domain\Manager\GroupManager;

class GroupAddEditAction
{
    /**
     * @var GroupRepository
     */
    private $repository;

    /**
     * @var GroupManager
     */
    private $manager;

    public function __construct(GroupRepository $repository, GroupManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    public function handleGroupEdit(GroupForm $form, string $groupId)
    {
        $group = $this->repository->assertFindById($groupId);
        $form->mapToGroup($group);

        $this->manager->storeChanges($group);
        return $group;
    }

    public function handleGroupCreation(GroupForm $form, string $groupId): BannerGroup
    {
        $group = new BannerGroup();
        $group->setId($groupId);

        $form->mapToGroup($group);

        $this->manager->storeChanges($group);
        return $group;
    }
}
