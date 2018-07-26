<?php declare(strict_types=1);

namespace Tests\Controller\Management;

use App\Controller\Management\BannerDeleteController;
use App\Domain\ActionHandler\BannerDeleteAction;
use App\Domain\Entity\BannerElement;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Manager\BannerManager;
use App\Domain\Repository\BannerRepository;
use App\Domain\Repository\GroupRepository;
use App\Tests\TestCase;

/**
 * @see BannerDeleteController
 */
class BannerDeleteControllerTest extends TestCase
{
    /**
     * Case: 404 - the subject banner does not exists already
     *
     * @see BannerDeleteController::handleDeleteAction()
     */
    public function testHandleChecksObjectExistence(): void
    {
        $repository = $this->createMock(BannerRepository::class);
        $repository->method('assertFindById')->willThrowException(new EntityNotFoundException('Banner not found'));
        $manager = new BannerManager($repository, $this->createMock(GroupRepository::class));

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            BannerDeleteAction::class,
            new BannerDeleteAction($manager)
        );

        // execute the action
        $client->request('DELETE', '/collective/element/cnt-cit');

        // assert valid response
        $this->assert404NotFoundResponse($client->getResponse());
    }

    /**
     * Case: Successful request
     *
     * @see BannerDeleteController::handleDeleteAction()
     */
    public function testHandle(): void
    {
        $repository = $this->createMock(BannerRepository::class);
        $repository->method('assertFindById')->willReturn(new BannerElement());
        $manager = new BannerManager($repository, $this->createMock(GroupRepository::class));

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            BannerDeleteAction::class,
            new BannerDeleteAction($manager)
        );

        // execute the action
        $client->request('DELETE', '/collective/element/cnt-cit');

        // assert valid response
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('{"message":"OK"}', $client->getResponse()->getContent());
    }
}
