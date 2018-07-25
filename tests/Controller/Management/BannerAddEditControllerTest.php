<?php declare(strict_types=1);

namespace Tests\Controller\Management;

use App\Controller\Management\BannerAddEditController;
use App\Domain\ActionHandler\BannerAddEditAction;
use App\Domain\Entity\BannerElement;
use App\Domain\Manager\BannerManager;
use App\Domain\Repository\BannerRepository;
use App\Tests\TestCase;

class BannerAddEditControllerTest extends TestCase
{
    /**
     * Case: Editing a Banner by providing a valid data
     *
     * @see BannerAddEditController::editBannerAction()
     */
    public function testEditBannerAction(): void
    {
        $manager = $this->createMock(BannerManager::class);
        $repository = $this->createMock(BannerRepository::class);
        $banner = new BannerElement();
        $banner->setId('iwa-ait-org');

        // mocks
        $repository->method('assertFindById')->willReturn($banner);

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            BannerAddEditAction::class,
            new BannerAddEditAction($manager, $repository)
        );

        // request
        $client->request('PUT', '/collective/element/edit/iwa-ait-org', [], [], [], json_encode([
            'url' => 'http://iwa-ait.org',
            'imageUrl' => 'http://iwa-ait.org/sites/default/files/iwaait_1.png',
            'expiresAt' => null,
            'active' => true,
            'title' => 'International Workers Association',
            'description' => 'The IWA programme promotes a form of non-hierarchical unionism which seeks to unite workers to fight for economic and political advances towards the final aim of libertarian communism.
                This federation is designed to both contest immediate industrial relations issues such as pay, working conditions and labor law, and pursue the reorganization of society into a global system of economic communes and administrative groups based within a system of federated free councils at local, regional, national and global levels. This reorganization would form the underlying structure of a self-managed society based on pre-planning and mutual aid—the establishment of anarchist communism.'
        ]));

        $this->assertContains('"id":"iwa-ait-org",', $client->getResponse()->getContent(), 'The id is not editable');
        $this->assertContains(
            '"description":"The IWA programme promotes a form of non-hierarchical unionism which seeks to unite workers to fight for economic and political advances towards the final aim of libertarian communism.',
            $client->getResponse()->getContent(),
            'Failed asserting that the description was inserted'
        );

        $this->assertSame(202, $client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function provideValidationTestCases(): array
    {
        return [
            'no any data submitted' => [
                'requestData' => [],
                'expectedResponseMessageParts' => [
                    '"active":["This value should not be blank."]',
                    '"title":["This value should not be blank."]',
                    '"url":["This value should not be blank."],',
                    '"This value should not be blank."'
                ]
            ]
        ];
    }

    /**
     * Case: Editing a Banner by providing a valid data
     *
     * @dataProvider provideValidationTestCases
     *
     * @see          BannerAddEditController::editBannerAction()
     *
     * @param array $requestData
     * @param array $expectedResponseMessageParts
     */
    public function testEditBannerActionValidation(array $requestData, array $expectedResponseMessageParts): void
    {
        $manager = $this->createMock(BannerManager::class);
        $repository = $this->createMock(BannerRepository::class);
        $banner = new BannerElement();
        $banner->setId('iwa-ait-org');

        // mocks
        $repository->method('assertFindById')->willReturn($banner);

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            BannerAddEditAction::class,
            new BannerAddEditAction($manager, $repository)
        );

        // request
        $client->request('PUT', '/collective/element/edit/iwa-ait-org', [], [], [], json_encode($requestData));

        foreach ($expectedResponseMessageParts as $part) {
            $this->assertContains($part, $client->getResponse()->getContent());
        }

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }

    /*public function testCreateBannerAction()
    {

    }*/
}
