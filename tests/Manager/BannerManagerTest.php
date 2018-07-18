<?php declare(strict_types = 1);

namespace App\Tests\Manager;

use App\Domain\Entity\BannerElement;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Entity\{BannerGroup};
use App\Domain\Exception\{ManagerException};
use App\Repository\{BannerElementRepository, GroupDoctrineRepository};
use App\Manager\BannerManager;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @see BannerManager
 */
class BannerManagerTest extends TestCase
{
    /**
     * @see BannerManager::createForGroup()
     */
    public function test_creates_element_with_preselected_group()
    {
        $manager = $this->createManagerWithRepositoryThatFindsObject();
        $createdElement = $manager->createForGroup('testGroup');

        $this->assertElementBelongsToGroupNamed('test', $createdElement);
    }

    /**
     * @see BannerManager::createForGroup()
     */
    public function test_finds_by_id()
    {
        $repository = $this->createMock(BannerElementRepository::class);
        $repository->method('find')->willReturn(
            (new BannerElement())
                ->setGroup(new BannerGroup())
                ->setId('test')
        );
        $em = $this->createMock(EntityManagerInterface::class);

        $manager = new BannerManager(
            $repository,
            $em,
            $this->createMock(GroupDoctrineRepository::class)
        );

        $this->assertValidBannerElement($manager->find('test', false));
    }

    /**
     * @see BannerManager::deleteById()
     */
    public function test_deletes_by_id()
    {
        $repository = $this->createMock(BannerElementRepository::class);
        $repository->method('find')->willReturn(new BannerElement());
        $em = $this->createMock(EntityManagerInterface::class);

        $manager = new BannerManager(
            $repository,
            $em,
            $this->createMock(GroupDoctrineRepository::class)
        );

        // asserts
        $em->expects($this->once())->method('remove');

        $manager->deleteById('test');
    }

    /**
     * @see BannerManager::deleteById()
     * @throws EntityNotFoundException
     */
    public function test_throws_exception_when_element_to_delete_was_not_found()
    {
        $manager = $this->createManagerWithEmptyRepository();

        // asserts
        $this->expectException(EntityNotFoundException::class);

        $manager->deleteById('test');
    }

    /**
     * @see BannerManager::find()
     */
    public function test_throws_exception_when_find_does_not_find_anything()
    {
        $manager = $this->createManagerWithEmptyRepository();

        // asserts
        $this->expectException(EntityNotFoundException::class);

        $manager->find('test', false);
    }

    /**
     * @see BannerManager::storeChanges()
     * @throws \App\Domain\Exception\ManagerException
     */
    public function test_asserts_that_element_belongs_to_group_when_saving()
    {
        $this->expectException(ManagerException::class);
        $this->expectExceptionMessageRegExp('/Missing banner group association/');

        $manager = $this->createManagerWithEmptyRepository();
        $manager->save(new BannerElement());
    }

    /**
     * @return BannerManager
     */
    private function createManagerWithEmptyRepository()
    {
        $repository = $this->createMock(BannerElementRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $manager = new BannerManager(
            $repository,
            $em,
            $this->createMock(GroupDoctrineRepository::class)
        );

        return $manager;
    }

    /**
     * @return BannerManager|MockObject
     */
    private function createManagerWithRepositoryThatFindsObject()
    {
        /**
         * @var BannerManager|MockObject $manager
         */
        $builder = $this->getMockBuilder(BannerManager::class);
        $builder->disableOriginalConstructor();
        $builder->setMethods(['getGroupRepository']);
        $manager = $builder->getMock();

        $group = new BannerGroup();
        $group->setId('test');

        $repository = $this->createMock(GroupDoctrineRepository::class);
        $repository->method('find')->willReturn($group);
        $manager->method('getGroupRepository')->willReturn($repository);

        return $manager;
    }
}
