<?php declare(strict_types=1);

namespace Tests\Controller\Management;

use App\Controller\Management\GroupAddEditController;
use App\Domain\ActionHandler\GroupAddEditAction;
use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\ManagerException;
use App\Domain\Manager\GroupManager;
use App\Domain\Repository\GroupRepository;
use App\Tests\TestCase;

/**
 * @see GroupAddEditController
 */
class GroupAddEditControllerTest extends TestCase
{
    /**
     * Case: Valid request, group was added
     *
     * @see GroupAddEditController::handleGroupCreationAction()
     */
    public function testHandleGroupCreationAction()
    {
        $repository = $this->createMock(GroupRepository::class);

        $client = self::createClient();
        $client->getContainer()->set(
            GroupAddEditAction::class,
            new GroupAddEditAction($repository, $this->createMock(GroupManager::class))
        );

        // request
        $data = $this->getValidGroupRequestWithoutId();
        $data['id'] = 'anarchism';

        // response
        $client->request('POST', '/collective/group/create', [], [], [], json_encode($data));
        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(201, $client->getResponse()->getStatusCode());

        foreach (['id', 'description', 'title'] as $fieldName) {
            $this->assertArrayHasKey($fieldName, $json, 'Field "' . $fieldName . '" not found in returned group');
        }
    }

    /**
     * Case: The group id was already taken
     *
     * @see GroupAddEditController::handleGroupCreationAction()
     */
    public function testHandleGroupCreationActionChecksForDuplicates(): void
    {
        $manager = $this->createMock(GroupManager::class);
        $manager->method('storeChanges')->willThrowException(
            new ManagerException('Already exists', ManagerException::ENTITY_ALREADY_EXISTS)
        );

        $client = self::createClient();
        $client->getContainer()->set(
            GroupAddEditAction::class,
            new GroupAddEditAction($this->createMock(GroupRepository::class), $manager)
        );

        // request
        $data = $this->getValidGroupRequestWithoutId();
        $data['id'] = 'anarchism';

        // response
        $client->request('POST', '/collective/group/create', [], [], [], json_encode($data));

        $this->assertSame('{"message":"Object already exists","code":80001}', $client->getResponse()->getContent());
        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }

    /**
     * @see GroupAddEditController::handleGroupEditAction()
     */
    public function testHandleGroupEditAction()
    {
        $repository = $this->createMock(GroupRepository::class);
        $repository->method('assertFindById')->willReturn(new BannerGroup());

        $client = self::createClient();
        $client->getContainer()->set(
            GroupAddEditAction::class,
            new GroupAddEditAction($repository, $this->createMock(GroupManager::class))
        );

        // response
        $client->request('PUT', '/collective/group/edit/anarchism', [], [], [], json_encode($this->getValidGroupRequestWithoutId()));
        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(202, $client->getResponse()->getStatusCode());

        foreach (['id', 'description', 'title'] as $fieldName) {
            $this->assertArrayHasKey($fieldName, $json, 'Field "' . $fieldName . '" not found in returned group');
        }
    }
}
