<?php declare(strict_types = 1);

namespace App\Tests\Manager;

use App\Entity\{BannerElement, BannerGroup};
use App\Exception\{EntityNotFoundException, ManagerException, NotDeletableEntityException};
use App\Manager\GroupManager;
use App\Repository\BannerGroupRepository;
use App\Tests\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockBuilder;

/**
 * @see GroupManager
 */
class GroupManagerTest extends TestCase
{
    /**
     * @see GroupManager::save()
     * @throws ManagerException
     */
    public function test_throws_exception_on_duplication()
    {
        // assert
        $this->expectException(ManagerException::class);

        // the ORM throws a proper exception when a duplication is found, we expect that it will be thrown
        // and then the GroupManager class will re-throw an application exception
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist')->willThrowException(new ConstraintViolationException('Constraint violation', $this->createMock(PDOException::class)));

        $manager = $this->createManager(function (MockBuilder $builder) use ($em) {
            $builder->setConstructorArgs([$em, $this->createMock(BannerGroupRepository::class)]);
            $builder->setMethodsExcept(['save']);
        });

        // execute the action
        $manager->save(new BannerGroup());
    }

    /**
     * @see GroupManager::deleteById()
     *
     * @throws NotDeletableEntityException
     * @throws \App\Exception\EntityNotFoundException
     */
    public function test_deletes_only_when_no_dependent_banners_connected()
    {
        // asserts
        $this->expectException(NotDeletableEntityException::class);

        // preparation: The repository must return a group that has connected elements
        $repository = $this->createMock(BannerGroupRepository::class);
        $repository->method('find')->willReturn(
            (new BannerGroup())
                ->setElements(new ArrayCollection([
                    new BannerElement(),
                    new BannerElement()
                ]))
        );

        // execute the action
        $manager = new GroupManager(
            $this->createMock(EntityManagerInterface::class),
            $repository
        );

        $manager->deleteById('test');
    }

    /**
     * @see GroupManager::save()
     */
    public function test_invokes_deletion_when_no_banners_under_group()
    {
        // preparation:
        //   - the repository returns a object without dependencies
        //   - EntityManager is expecting a call to remove()
        $repository = $this->createMock(BannerGroupRepository::class);
        $repository->method('find')->willReturn(
            (new BannerGroup())
                ->setElements(new ArrayCollection([]))
        );

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('remove');

        // execute the action
        $manager = new GroupManager(
            $em,
            $repository
        );

        $manager->deleteById('test');
    }


    /**
     * @see GroupManager::findOrCreateNew()
     * @throws \App\Exception\EntityNotFoundException
     */
    public function test_find_creates_new_group_with_proper_id()
    {
        // execute the action
        $manager = new GroupManager(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(BannerGroupRepository::class)
        );

        $this->assertGroupHasId(
            'this-is-a-test-group-name',
            $manager->findOrCreateNew('this-is-a-test-group-name', true)
        );
    }

    /**
     * @see GroupManager::findOrCreateNew()
     * @throws EntityNotFoundException
     */
    public function test_checks_existence_of_group_on_find()
    {
        // asserts
        $this->expectException(EntityNotFoundException::class);

        // repository should not find any object
        $repository = $this->createMock(BannerGroupRepository::class);
        $repository->method('find')->willReturn(null);

        $manager = new GroupManager(
            $this->createMock(EntityManagerInterface::class),
            $repository
        );

        $manager->findOrCreateNew('test-id', false);
    }

    /**
     * @param callable $preparation
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|GroupManager
     */
    private function createManager(callable $preparation)
    {
        $builder = $this->getMockBuilder(GroupManager::class);
        $preparation($builder);

        return $builder->getMock();
    }
}
