<?php declare(strict_types = 1);

namespace App\Manager;

use App\Entity\BannerGroup;
use App\Exception\EntityNotFoundException;
use App\Exception\ManagerException;
use App\Exception\NotDeletableEntityException;
use App\Repository\BannerGroupRepository;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @see BannerGroup
 * @see BannerGroupRepository
 */
class GroupManager implements ManagerInterface
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var BannerGroupRepository $repository
     */
    private $repository;

    public function __construct(EntityManagerInterface $em, BannerGroupRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function getEntityClassName(): string
    {
        return BannerGroup::class;
    }

    /**
     * @return BannerGroupRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Creates a BannerGroup object (new or existing from storage)
     * always with an id
     *
     * @param string $name
     * @param bool   $createNew Should create a new element or find existing?
     *
     * @return BannerGroup
     * @throws EntityNotFoundException
     */
    public function findOrCreateNew(string $name = '', bool $createNew)
    {
        if (!$createNew) {
            $group = $this->getRepository()->find($name);

            if (!$group instanceof BannerGroup) {
                throw new EntityNotFoundException('Requested banner group does not exists');
            }

            return $group;
        }

        $group = new BannerGroup();

        if ($name !== '') {
            $group->setId($name);
        }

        return $group;
    }

    /**
     * @param BannerGroup $group
     * @throws ManagerException
     */
    public function save($group)
    {
        try {
            $this->em->persist($group);
            $this->em->flush();

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
    public function deleteById(string $groupName)
    {
        $group = $this->getRepository()->find($groupName);

        if (!$group instanceof BannerGroup) {
            throw new EntityNotFoundException('Cannot find banner for given id');
        }

        if ($group->getElements()->count() > 0) {
            throw new NotDeletableEntityException('The group has at least 1 banner element attached', NotDeletableEntityException::AT_LEAST_ONE_ITEM_EXISTING);
        }

        $this->em->remove($group);
        $this->em->flush();
    }
}
