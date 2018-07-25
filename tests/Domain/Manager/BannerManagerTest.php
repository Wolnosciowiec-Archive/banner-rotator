<?php declare(strict_types=1);

namespace Tests\Domain\Manager;

use App\Domain\Entity\BannerElement;
use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\ManagerException;
use App\Domain\Manager\BannerManager;
use App\Domain\Repository\BannerRepository;
use App\Domain\Repository\GroupRepository;
use App\Tests\TestCase;

class BannerManagerTest extends TestCase
{
    /**
     * @see BannerManager::storeChanges()
     */
    public function testStoreChanges(): void
    {
        $manager = new BannerManager(
            $this->createMock(BannerRepository::class),
            $this->createMock(GroupRepository::class)
        );

        $banner = new BannerElement();
        $banner->setGroup(new BannerGroup());

        $manager->storeChanges($banner);
        $this->assertTrue(true, 'No exception thrown');
    }

    /**
     * @see BannerManager::storeChanges()
     */
    public function testStoreChangesThrowsException(): void
    {
        $this->expectException(ManagerException::class);

        $manager = new BannerManager(
            $this->createMock(BannerRepository::class),
            $this->createMock(GroupRepository::class)
        );

        $manager->storeChanges(new BannerElement());
    }

    /**
     * @see BannerManager::deleteById()
     */
    public function testDeleteById(): void
    {
        $repository = $this->createMock(BannerRepository::class);
        $repository->expects($this->once())->method('assertFindById');
        $repository->expects($this->once())->method('remove');
        $repository->expects($this->once())->method('flush');

        $manager = new BannerManager(
            $repository,
            $this->createMock(GroupRepository::class)
        );

        $manager->deleteById('123-456-789');
    }

    /**
     * @see BannerManager::createForGroup()
     */
    public function testCreateForGroup()
    {
        $group = new BannerGroup();
        $group->setId('161');

        $groupRepository = $this->createMock(GroupRepository::class);
        $groupRepository->method('assertFindById')->willReturn($group);

        $manager = new BannerManager(
            $this->createMock(BannerRepository::class),
            $groupRepository
        );

        $banner = $manager->createForGroup('161');

        $this->assertSame(
            '161',
            $banner->getBannerGroup()->getId(),
            'Expected that the Banner Element will be attached to a proper group'
        );
    }
}
