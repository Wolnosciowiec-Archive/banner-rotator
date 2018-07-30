<?php declare(strict_types=1);

namespace Tests\Infrastructure\Repository;

use App\Domain\Entity\BannerElement;
use App\Domain\Entity\BannerGroup;
use App\Infrastructure\Repository\BannerDoctrineRepository;
use App\Tests\TestCase;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @see BannerDoctrineRepository
 */
class BannerDoctrineRepositoryTest extends TestCase
{
    /**
     * @see BannerDoctrineRepository::findAllBanners()
     */
    public function testFindAllBanners()
    {
        /**
         * @var BannerDoctrineRepository $repository
         * @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb
         */
        [$repository, $qb] = $this->createMockedDependencies();

        $repository->findAllBanners(new BannerGroup());
        $dql = $qb->getDQL();

        $this->assertContains('banner.bannerGroup = :group', $dql);
        $this->assertContains('banner.id DESC', $dql);
    }

    /**
     * @see BannerDoctrineRepository::findPublishedBanners()
     */
    public function testFindPublishedBanners()
    {
        /**
         * @var BannerDoctrineRepository $repository
         * @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb
         */
        [$repository, $qb] = $this->createMockedDependencies();

        $repository->findPublishedBanners(new BannerGroup(), 400);
        $dql = $qb->getDQL();

        $this->assertContains('banner.bannerGroup = :group', $dql);
        $this->assertContains('banner.active = true', $dql);
        $this->assertContains('banner.expiresAt > CURRENT_TIMESTAMP() OR banner.expiresAt is null', $dql);
        $this->assertContains('banner.id DESC', $dql);
    }

    private function createMockedDependencies(): array
    {
        self::createClient();
        $workingEntityManager = self::$container->get('doctrine.orm.entity_manager');

        // dependencies for ServiceEntityRepository
        $registry = $this->createMock(RegistryInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);

        // QueryBuilder dependencies
        $query = $this->createMock(AbstractQuery::class);
        $query->method('getSingleResult')->willReturn(new BannerElement());
        $query->method('getResult')->willReturn([]);

        // QueryBuilder for asserts on DQL
        $qbBuilder = $this->getMockBuilder(QueryBuilder::class);
        $qbBuilder->setConstructorArgs([$workingEntityManager]);
        $qbBuilder->setMethods(['getQuery']);
        $queryBuilder = $qbBuilder->getMock();
        $queryBuilder->method('getQuery')->willReturn($query);

        $em->method('createQueryBuilder')->willReturn($queryBuilder);
        $registry->method('getManagerForClass')->willReturn($em);
        $em->method('getClassMetadata')->willReturn(
            $workingEntityManager->getClassMetadata(BannerElement::class)
        );

        $repository = new BannerDoctrineRepository($registry);

        return [$repository, $queryBuilder];
    }
}
