<?php declare(strict_types = 1);

namespace App\Tests;

use App\Domain\Entity\BannerElement;
use App\Domain\Entity\BannerGroup;

abstract class TestCase extends \PHPUnit\Framework\TestCase
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
}
