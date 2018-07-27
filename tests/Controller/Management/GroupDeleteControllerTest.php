<?php declare(strict_types=1);

namespace Tests\Controller\Management;

use App\Controller\Management\GroupDeleteController;
use App\Domain\ActionHandler\GroupDeleteAction;
use App\Domain\Entity\BannerElement;
use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Manager\GroupManager;
use App\Domain\Repository\GroupRepository;
use App\Tests\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @see GroupDeleteController
 */
class GroupDeleteControllerTest extends TestCase
{
    /**
     * Case: The group was properly found, its empty and was just deleted
     *
     * @see GroupDeleteController::handleDeleteAction()
     */
    public function testHandleDeleteAction(): void
    {
        // empty grup
        $group = new BannerGroup();
        $group->setElements(new ArrayCollection());

        // mock
        $repository = $this->createMock(GroupRepository::class);
        $repository->method('assertFindById')->willReturn($group);
        $manager = new GroupManager($repository);

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            GroupDeleteAction::class,
            new GroupDeleteAction($manager)
        );

        // execute the action
        $client->request('DELETE', '/collective/group/money-economy');

        // assert valid response
        $this->assertOKTypeResponse($client->getResponse());
    }

    /**
     * Case: Cannot delete a group when children entries exists (Banner elements)
     *
     * @see GroupDeleteController::handleDeleteAction()
     */
    public function testHandleDeleteActionChecksForChildrenBanners(): void
    {
        // group will have one element inside
        $group = new BannerGroup();
        $group->setElements(new ArrayCollection());
        $group->getElements()->add(new BannerElement());

        // mock
        $repository = $this->createMock(GroupRepository::class);
        $repository->method('assertFindById')->willReturn($group);
        $manager = new GroupManager($repository);

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            GroupDeleteAction::class,
            new GroupDeleteAction($manager)
        );

        // execute the action
        $client->request('DELETE', '/collective/group/money-economy');

        // assert valid response
        $this->assertNotDeletableResponse($client->getResponse());
    }

    /**
     * Case: The group cannot be deleted because it does not exists already
     *
     * @see GroupDeleteController::handleDeleteAction()
     */
    public function testHandleDeleteActionChecksIfGroupExists(): void
    {
        // mock
        $repository = $this->createMock(GroupRepository::class);
        $repository->method('assertFindById')->willThrowException(new EntityNotFoundException());
        $manager = new GroupManager($repository);

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            GroupDeleteAction::class,
            new GroupDeleteAction($manager)
        );

        // execute the action
        $client->request('DELETE', '/collective/group/money-economy');

        // assert valid response
        $this->assert404NotFoundResponse($client->getResponse());
    }
}
