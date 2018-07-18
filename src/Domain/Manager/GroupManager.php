<?php declare(strict_types = 1);

namespace App\Domain\Manager;

use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Repository\GroupRepository;
use App\Domain\Exception\ManagerException;
use App\Domain\Exception\NotDeletableEntityException;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @see BannerGroup
 */
class GroupManager
{
    /**
     * @var GroupRepository $repository
     */
    private $repository;

    public function __construct(EntityManagerInterface $em, GroupRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param BannerGroup $group
     * @throws ManagerException
     */
    public function storeChanges($group): void
    {
        try {
            $this->repository->persist($group);
            $this->repository->flush();

        } catch (ConstraintViolationException $exception) {
            throw new ManagerException('Group with given id already exists', ManagerException::ENTITY_ALREADY_EXISTS);
        }
    }

    /**
     * @param string $groupName
     *
     * @throws NotDeletableEntityException
     * @throws EntityNotFoundException
     */
    public function deleteById(string $groupName): void
    {
        $group = $this->repository->assertFindById($groupName);

        if ($group->getElements()->count() > 0) {
            throw new NotDeletableEntityException('The group has at least 1 banner element attached', NotDeletableEntityException::AT_LEAST_ONE_ITEM_EXISTING);
        }

        $this->repository->remove($group);
        $this->repository->flush();
    }
}
