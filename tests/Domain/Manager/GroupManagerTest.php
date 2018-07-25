<?php declare(strict_types=1);

namespace Tests\Domain\Manager;

use App\Domain\Entity\BannerElement;
use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\ManagerException;
use App\Domain\Exception\NotDeletableEntityException;
use App\Domain\Manager\GroupManager;
use App\Domain\Repository\GroupRepository;
use App\Tests\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ConstraintViolationException;

class GroupManagerTest extends TestCase
{
    /**
     * @see GroupManager::storeChanges()
     */
    public function testStoreChanges(): void
    {
        $repository = $this->createMock(GroupRepository::class);
        $repository->expects($this->once())->method('persist');

        $manager = new GroupManager($repository);
        $manager->storeChanges(new BannerGroup());
    }

    /**
     * @see GroupManager::storeChanges()
     */
    public function testStoreChangesWillThrowTranslatedExceptionWhenDoctrineRepositoryThrowsException(): void
    {
        $this->expectException(ManagerException::class);

        $repository = $this->createMock(GroupRepository::class);
        $repository->method('persist')->willReturnCallback(function () {
            throw new class () extends ConstraintViolationException {
                public function __construct() { }
            };
        });

        $manager = new GroupManager($repository);
        $manager->storeChanges(new BannerGroup());
    }

    /**
     * @see GroupManager::deleteById()
     */
    public function testDeleteById(): void
    {
        $group = new BannerGroup();
        $group->setId('123');
        $group->setElements(new ArrayCollection());

        $repository = $this->createMock(GroupRepository::class);
        $repository->method('assertFindById')->willReturn($group);
        $repository->expects($this->once())->method('remove');

        $manager = new GroupManager($repository);
        $manager->deleteById('123');
    }

    /**
     * @see GroupManager::deleteById()
     */
    public function testDeleteByIdChecksDependencies(): void
    {
        $this->expectException(NotDeletableEntityException::class);

        // if the group will contain 1 or more children elements then it cannot be deleted
        $group = new BannerGroup();
        $group->setId('123');
        $group->setElements(new ArrayCollection());
        $group->getElements()->add(new BannerElement());

        $repository = $this->createMock(GroupRepository::class);
        $repository->method('assertFindById')->willReturn($group);

        $manager = new GroupManager($repository);
        $manager->deleteById('123');
    }
}
