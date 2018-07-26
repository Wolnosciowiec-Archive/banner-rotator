<?php declare(strict_types = 1);

namespace App\Tests;

use App\Domain\Entity\BannerElement;
use App\Domain\Entity\BannerGroup;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class TestCase extends WebTestCase
{
    /**
     * @param string $group
     * @param BannerElement $element
     */
    protected function assertElementBelongsToGroupNamed(string $group, BannerElement $element)
    {
        $this->assertSame($group, $element->getBannerGroup()->getId());
    }

    /**
     * @param BannerElement|mixed $element
     */
    protected function assertValidBannerElement($element)
    {
        $this->assertInstanceOf(BannerElement::class, $element);
        $this->assertInternalType('string', $element->getId());
        $this->assertNotEmpty($element->getId());
        $this->assertInstanceOf(BannerGroup::class, $element->getBannerGroup());
    }

    /**
     * @param string $expectedId
     * @param BannerGroup|mixed $group
     */
    protected function assertGroupHasId(string $expectedId, $group)
    {
        $this->assertInstanceOf(BannerGroup::class, $group);
        $this->assertSame($expectedId, $group->getId());
    }

    protected function getValidBannerRequestBodyWithoutId(): array
    {
        return [
            'url' => 'http://iwa-ait.org',
            'imageUrl' => 'http://iwa-ait.org/sites/default/files/iwaait_1.png',
            'expiresAt' => null,
            'active' => true,
            'title' => 'International Workers Association',
            'description' => 'The IWA programme promotes a form of non-hierarchical unionism which seeks to unite workers to fight for economic and political advances towards the final aim of libertarian communism.
                This federation is designed to both contest immediate industrial relations issues such as pay, working conditions and labor law, and pursue the reorganization of society into a global system of economic communes and administrative groups based within a system of federated free councils at local, regional, national and global levels. This reorganization would form the underlying structure of a self-managed society based on pre-planning and mutual aid—the establishment of anarchist communism.'
        ];
    }

    protected function getExampleBanner(): BannerElement
    {
        /**
         * @var BannerElement $banner
         */
        $banner = self::$container->get('jms_serializer')->deserialize(
            json_encode($this->getValidBannerRequestBodyWithoutId()),
            BannerElement::class,
            'json'
        );

        $banner->setId('iwa-ait-org');

        return $banner;
    }

    protected function assert404NotFoundResponse(Response $response): void
    {
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('{"message":"Object not found"}', $response->getContent());
    }
}
