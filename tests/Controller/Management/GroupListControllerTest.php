<?php declare(strict_types=1);

namespace Tests\Controller\Management;

use App\Controller\Management\GroupListController;
use App\Domain\ActionHandler\GroupListAction;
use App\Domain\Entity\BannerGroup;
use App\Domain\Repository\GroupRepository;
use App\Tests\TestCase;

/**
 * @see GroupListController
 */
class GroupListControllerTest extends TestCase
{
    /**
     * @see GroupListController::handleGroupsListingAction()
     */
    public function testHandleGroupsListingAction(): void
    {
        $workerGroup = new BannerGroup();
        $workerGroup->setId('labour-movement');
        $workerGroup->setTitle('Workers movement');
        $workerGroup->setDescription('Liberate the world, let everyone gain the freedom. Starting from your workplace, ' .
                               'where you spend half of your life (or more). Organize with other employees to create ' .
                               'a solidarity structure, and take over the workplace, you will no longer need your boss ' .
                               'that steals your fruits of work. You and other producers can handle it, let\'s cooperate!');

        $tenantGroup = new BannerGroup();
        $tenantGroup->setId('housing');
        $tenantGroup->setTitle('Tenants rights protection');
        $tenantGroup->setDescription('Having a home is a very basic right. It is not acceptable that bourgeois assholes are having ' .
                                     'multiple flats or houses. They rent them to poorer people to get their money. There is a transfer ' .
                                     'of money from people who do not have it much to people who have it too much. That\'s accumulation of capital. ' .
                                     'Everybody deserves to have a home, not to sleep in a car because of sick economy. We are blocking evictions.');

        // mock
        $repository = $this->createMock(GroupRepository::class);
        $repository->method('findAll')->willReturn([$workerGroup, $tenantGroup]);

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            GroupListAction::class,
            new GroupListAction($repository)
        );

        // execute the action
        $client->request('GET', '/collective/group');
        $json = json_decode($client->getResponse()->getContent(), true);

        // assertions
        $this->assertCount(2, $json);

        foreach ($json as $group) {
            foreach (['id', 'active', 'description', 'title', 'dateAdded'] as $fieldName) {
                $this->assertArrayHasKey($fieldName, $group, 'Field "' . $fieldName . '" not found in a returned group');
            }
        }
    }
}
