<?php declare(strict_types=1);

namespace Tests\Controller\PublicSide;

use App\Controller\PublicSide\BrowseController;
use App\Domain\ActionHandler\BrowseAction;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Repository\BannerRepository;
use App\Domain\Repository\GroupRepository;
use App\Tests\TestCase;

/**
 * @see BrowseController
 */
class BrowseControllerTest extends TestCase
{
    /**
     * Case: Valid response with custom settings in the URL
     *
     * @see BrowseController::browseRenderedAction()
     */
    public function testBrowseRenderedAction(): void
    {
        $client = self::createClient();

        // mock
        $repository = $this->createMock(BannerRepository::class);
        $repository->method('findPublishedBanners')->willReturn([$this->getExampleBanner()]);

        // connecting pieces together
        $client->getContainer()->set(
            BrowseAction::class,
            new BrowseAction($this->createMock(GroupRepository::class), $repository)
        );

        $client->request('GET', '/public/browse/anarchism.html?max_width=500&custom_css_url=http://some.domain/some.css');
        $html = $client->getResponse()->getContent();

        /**
         * @see getExampleBanner
         */
        $this->assertContains(
            '<img src="http://iwa-ait.org/sites/default/files/iwaait_1.png"',
            $html,
            'Failed asserting that image is present'
        );

        $this->assertContains(
            'alt="International Workers Association"',
            $html,
            'Failed asserting that title is present'
        );

        $this->assertContains(
            'reorganization of society into a global system of economic communes and administrative groups based within a system of federated free councils at local, regional, national and global levels',
            $html,
            'Failed asserting that the description is present'
        );

        $this->assertContains('rel="stylesheet" href="http://some.domain/some.css"', $html);
        $this->assertContains('max-width: 500px;', $html);
    }

    /**
     * Case: All options are fallback to defaults
     *
     * @see BrowseController::browseRenderedAction()
     */
    public function testBrowseRenderedActionWithoutCustomOptions(): void
    {
        $client = self::createClient();

        // mock
        $repository = $this->createMock(BannerRepository::class);
        $repository->method('findPublishedBanners')->willReturn([$this->getExampleBanner()]);

        // connecting pieces together
        $client->getContainer()->set(
            BrowseAction::class,
            new BrowseAction($this->createMock(GroupRepository::class), $repository)
        );

        $client->request('GET', '/public/browse/anarchism.html');
        $html = $client->getResponse()->getContent();

        $this->assertNotContains('max-width: 500px;', $html);
        $this->assertNotContains('rel="stylesheet" href="http://some.domain/some.css', $html);
        $this->assertNotContains('rel="stylesheet" href=""', $html);
    }

    /**
     * Case: Custom 404 not found page
     *
     * @see BrowseController::browseRenderedAction()
     */
    public function testBrowseRenderedActionShowsErrorMessageInHtmlFormat(): void
    {
        $client = self::createClient();

        // mock
        $repository = $this->createMock(BannerRepository::class);
        $repository->method('findPublishedBanners')->willReturn([]);

        $groupRepository = $this->createMock(GroupRepository::class);
        $groupRepository->method('assertFindById')->willThrowException(new EntityNotFoundException());

        // connecting pieces together
        $client->getContainer()->set(
            BrowseAction::class,
            new BrowseAction($groupRepository, $repository)
        );

        $client->request('GET', '/public/browse/anarchism.html');
        $html = $client->getResponse()->getContent();

        $this->assertContains('Invalid banner group given in the url address', $html);
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Case: Valid JSON returned
     *
     * @see BrowseController::browseJsonAction()
     */
    public function testBrowseJsonAction(): void
    {
        $client = self::createClient();

        // mock
        $repository = $this->createMock(BannerRepository::class);
        $repository->method('findPublishedBanners')->willReturn([$this->getExampleBanner()]);

        // connecting pieces together
        $client->getContainer()->set(
            BrowseAction::class,
            new BrowseAction($this->createMock(GroupRepository::class), $repository)
        );

        $client->request('GET', '/public/browse/anarchism.json');
        $json = json_decode($client->getResponse()->getContent(), true);

        /**
         * @see getExampleBanner
         */
        $this->assertContains(
            'form of non-hierarchical unionism which seeks to unite workers to fight for economic and political advances towards the final aim of libertarian communism',
            $client->getResponse()->getContent(),
            'Failed asserting that the description is present in the response'
        );

        $this->assertSame('http://iwa-ait.org', $json['elements'][0]['url']);
        $this->assertSame(['url', 'title', 'imageUrl', 'description'], array_keys($json['elements'][0]));
    }

    /**
     * Case: 404 page in JSON
     *
     * @see BrowseController::browseJsonAction()
     */
    public function testBrowseJsonActionShows404Page(): void
    {
        $client = self::createClient();

        $repository = $this->createMock(BannerRepository::class);
        $repository->method('findPublishedBanners')->willReturn([]);

        $groupRepository = $this->createMock(GroupRepository::class);
        $groupRepository->method('assertFindById')->willThrowException(new EntityNotFoundException());

        // connecting pieces together
        $client->getContainer()->set(
            BrowseAction::class,
            new BrowseAction($groupRepository, $repository)
        );

        $client->request('GET', '/public/browse/there-is-no-such-thing-as-anarchocapitalism.json');

        // asserts
        $this->assert404NotFoundResponse($client->getResponse());
    }
}
