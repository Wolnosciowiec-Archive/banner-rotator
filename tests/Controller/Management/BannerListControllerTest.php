<?php declare(strict_types=1);

namespace Tests\Controller\Management;

use App\Controller\Management\BannerDeleteController;
use App\Controller\Management\BannerListController;
use App\Domain\ActionHandler\BannerListAction;
use App\Domain\Entity\BannerElement;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Repository\BannerRepository;
use App\Domain\Repository\GroupRepository;
use App\Tests\TestCase;

/**
 * @see BannerListController
 */
class BannerListControllerTest extends TestCase
{
    /**
     * Case: 200 - lists all banners properly
     *
     * @see BannerListController::listBannersAction()
     */
    public function testListBannersAction(): void
    {
        $repository = $this->createMock(BannerRepository::class);
        $groupRepository = $this->createMock(GroupRepository::class);

        $client = self::createClient();

        // mock
        $repository->method('findAllBanners')->willReturn([
            $this->getExampleBanner()
        ]);

        $client->getContainer()->set(
            BannerListAction::class,
            new BannerListAction($groupRepository, $repository)
        );

        $client->request('GET', '/collective/element/listing/employees-self-management-workplaces');
        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(
            [
                0 => [
                    'id' => 'iwa-ait-org',
                    'url' => 'http://iwa-ait.org',
                    'title' => 'International Workers Association',
                    'imageUrl' => 'http://iwa-ait.org/sites/default/files/iwaait_1.png',
                    'description' => 'The IWA programme promotes a form of non-hierarchical unionism which seeks to unite workers to fight for economic and political advances towards the final aim of libertarian communism.
                This federation is designed to both contest immediate industrial relations issues such as pay, working conditions and labor law, and pursue the reorganization of society into a global system of economic communes and administrative groups based within a system of federated free councils at local, regional, national and global levels. This reorganization would form the underlying structure of a self-managed society based on pre-planning and mutual aid—the establishment of anarchist communism.',
                    'active' => true
                ]
            ],
            $json
        );
    }

    /**
     * Case: 404 - the group does not exists
     *
     * @see BannerListController::listBannersAction()
     */
    public function testListBannersActionCheckIfTheGroupExists(): void
    {
        $repository = $this->createMock(BannerRepository::class);
        $groupRepository = $this->createMock(GroupRepository::class);
        $groupRepository->method('assertFindById')->willThrowException(new EntityNotFoundException('Banner not found'));

        // mock
        $client = self::createClient();
        $client->getContainer()->set(
            BannerListAction::class,
            new BannerListAction($groupRepository, $repository)
        );

        $client->request('GET', '/collective/element/listing/employees-self-management-workplaces');

        $this->assert404NotFoundResponse($client->getResponse());
    }
}
