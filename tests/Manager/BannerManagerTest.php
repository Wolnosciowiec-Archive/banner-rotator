<?php declare(strict_types = 1);

namespace App\Tests\Manager;

use App\Entity\BannerElement;
use App\Entity\BannerGroup;
use App\Exception\EntityNotFoundException;
use App\Manager\BannerManager;
use App\Repository\BannerElementRepository;
use App\Repository\BannerGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BannerManagerTest extends TestCase
{
	/**
	 * @see BannerManager::createForGroup()
	 */
    public function test_creates_element_with_preselected_group()
    {
        $manager = $this->createManagerWithRepositoryThatFindsObject();
        $createdElement = $manager->createForGroup('testGroup');

        $this->assertElementBelongsToGroupNamed('test', $createdElement);
    }

	/**
	 * @see BannerManager::deleteById()
	 */
    public function test_deletes_by_id()
    {
    	$repository = $this->createMock(BannerElementRepository::class);
    	$repository->method('find')->willReturn(new BannerElement());
    	$em = $this->createMock(EntityManagerInterface::class);

    	$manager = new BannerManager(
    		$repository,
		    $em,
		    $this->createMock(BannerGroupRepository::class)
	    );

    	// asserts
    	$em->expects($this->once())->method('remove');

    	$manager->deleteById('test');
    }

	/**
	 * @see BannerManager::deleteById()
	 */
	public function test_throws_exception_when_element_to_delete_was_not_found()
	{
		$repository = $this->createMock(BannerElementRepository::class);
		$em = $this->createMock(EntityManagerInterface::class);

		$manager = new BannerManager(
			$repository,
			$em,
			$this->createMock(BannerGroupRepository::class)
		);

		// asserts
		$this->expectException(EntityNotFoundException::class);

		$manager->deleteById('test');
	}
    
    private function assertElementBelongsToGroupNamed(string $group, BannerElement $element)
    {
        $this->assertSame($group, $element->getBannerGroup()->getId());
    }

    /**
     * @return BannerManager|MockObject
     */
    private function createManagerWithRepositoryThatFindsObject()
    {
        /**
         * @var BannerManager|MockObject $manager
         */
        $builder = $this->getMockBuilder(BannerManager::class);
        $builder->disableOriginalConstructor();
        $builder->setMethods(['getGroupRepository']);
        $manager = $builder->getMock();

        $group = new BannerGroup();
        $group->setId('test');

        $repository = $this->createMock(BannerGroupRepository::class);
        $repository->method('find')->willReturn($group);
        $manager->method('getGroupRepository')->willReturn($repository);

        return $manager;
    }
}
