<?php declare(strict_types=1);

namespace Tests\Controller\Management;

use App\Controller\Management\BannerAddEditController;
use App\Domain\ActionHandler\BannerAddEditAction;
use App\Domain\Entity\BannerElement;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Manager\BannerManager;
use App\Domain\Repository\BannerRepository;
use App\Tests\TestCase;

class BannerAddEditControllerTest extends TestCase
{
    /**
     * Case: Editing a Banner by providing a valid data (202)
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
        $client->request('PUT', '/collective/element/edit/iwa-ait-org', [], [], [], json_encode($this->getValidBannerRequestBodyWithoutId()));

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
                    '"title":["This value should not be blank."]',
                    '"url":["This value should not be blank."],',
                    '"This value should not be blank."'
                ]
            ],

            'Active field submitted with incorrect value, title is too short' => [
                'requestData' => [
                    'active' => null,
                    'title'  => '.'
                ],
                'expectedResponseMessageParts' => [
                    'This value is too short. It should have 3 characters or more.',
                    '"url":["This value should not be blank."',
                ]
            ],

            'Not an URL' => [
                'requestData' => [
                    'active' => true,
                    'title'  => 'Workers self-management',
                    'description' =>
                        'A form of organizational management based on self-directed work processes on the part of an organization\'s workforce.  ' .
                        'Self-management is a characteristic of many forms of socialism, with proposals for self-management having appeared many ' .
                        'times throughout the history of the socialist movement, advocated variously by market socialists, communists, and anarchists',
                    'url' => 'ohhh-thaats-not-an-url',
                    'imageUrl' => 'ohhh-this-does-not-point-to-an-image'
                ],
                'expectedResponseMessageParts' => [
                    '"url":["This value is not a valid URL."]',
                    '"imageUrl":["This value is not a valid URL."]'
                ]
            ],
        ];


    }

    /**
     * Case: Validation error (400)
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

    /**
     * Case: 404 - object not found
     *
     * @see BannerAddEditController::editBannerAction()
     */
    public function testEditBannerActionChecksForBannerExistence(): void
    {
        $manager = $this->createMock(BannerManager::class);
        $repository = $this->createMock(BannerRepository::class);

        // mocks
        $repository->method('assertFindById')->willThrowException(new EntityNotFoundException('Banner not found'));

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            BannerAddEditAction::class,
            new BannerAddEditAction($manager, $repository)
        );

        $client->request('PUT', '/collective/element/edit/non-existing-banner-there', [], [], [], json_encode($this->getValidBannerRequestBodyWithoutId()));

        $this->assert404NotFoundResponse($client->getResponse());
    }

    /**
     * Case: All fields valid, object created (201)
     *
     * @see BannerAddEditController::createBannerAction()
     */
    public function testCreateBannerAction(): void
    {
        $manager = $this->createMock(BannerManager::class);
        $repository = $this->createMock(BannerRepository::class);

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            BannerAddEditAction::class,
            new BannerAddEditAction($manager, $repository)
        );

        $body = $this->getValidBannerRequestBodyWithoutId();
        $client->request('POST', '/collective/element/create/anarchism', [], [], [], json_encode($body));

        $this->assertSame(201, $client->getResponse()->getStatusCode());
    }

    /**
     * Case: "Creation" endpoint checks for common validation rules as edit
     *
     * @see BannerAddEditController::createBannerAction()
     */
    public function testCreateBannerActionValidatesUsingSameRulesAsInEdit(): void
    {
        $manager = $this->createMock(BannerManager::class);
        $repository = $this->createMock(BannerRepository::class);

        // connecting pieces together
        $client = self::createClient();
        $client->getContainer()->set(
            BannerAddEditAction::class,
            new BannerAddEditAction($manager, $repository)
        );

        // note: $body does not have 'id'
        $body = $this->getValidBannerRequestBodyWithoutId();

        // let we unset the title, to see if the validation inherited from "Edit" endpoint is there
        unset($body['title']);

        $client->request('POST', '/collective/element/create/anarchism', [], [], [], json_encode($body));

        $this->assertContains('"title":["This value should not be blank."', $client->getResponse()->getContent());
        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }
}
