<?php declare(strict_types = 1);

namespace App\Manager;

use App\Entity\BannerElement;
use App\Entity\BannerGroup;
use App\Exception\ApplicationException;
use App\Exception\EntityNotFoundException;
use App\Exception\ManagerException;
use App\Repository\BannerElementRepository;
use App\Repository\BannerGroupRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Manages banners
 */
class BannerManager implements ManagerInterface
{
    /**
     * @var BannerElementRepository $repository
     */
    protected $repository;

    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    /**
     * @var BannerGroupRepository $groupRepository
     */
    protected $groupRepository;

    public function __construct(
        BannerElementRepository $bannerElementRepository,
        EntityManagerInterface $entityManager,
        BannerGroupRepository $bannerGroupRepository
    ) {
        $this->repository      = $bannerElementRepository;
        $this->em              = $entityManager;
        $this->groupRepository = $bannerGroupRepository;
    }

    /**
     * @inheritdoc
     */
    public function getEntityClassName(): string
    {
        return BannerElement::class;
    }

    /**
     * @return BannerElementRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return BannerGroupRepository
     */
    protected function getGroupRepository(): BannerGroupRepository
    {
        return $this->groupRepository;
    }

    /**
     * @param string $id
     * @param bool $createNew
     *
     * @return BannerElement
     * @throws EntityNotFoundException
     * @throws ApplicationException
     */
    public function findOrCreateNew(string $id, bool $createNew)
    {
        if ($createNew) {
            throw new ApplicationException('Creation is not implemented');
        }

        $banner = $this->getRepository()->find($id);

        if (!$banner instanceof BannerElement) {
            throw new EntityNotFoundException('Requested banner does not exists');
        }

        return $banner;
    }

    /**
     * Create a banner element under a selected group
     *
     * @param string $groupId
     *
     * @return BannerElement
     * @throws EntityNotFoundException
     */
    public function createForGroup(string $groupId): BannerElement
    {
        $group = $this->getGroupRepository()->find($groupId);

        if (!$group instanceof BannerGroup) {
            throw new EntityNotFoundException('Group "' .$groupId . '" does not exists');
        }

        $banner = new BannerElement();
        $banner->setGroup($group);

        return $banner;
    }

    /**
     * @param string $bannerId
     * @throws EntityNotFoundException
     */
    public function deleteById(string $bannerId)
    {
        $banner = $this->getRepository()->find($bannerId);
        
        if ($banner instanceof BannerElement) {
            $this->em->remove($banner);
            $this->em->flush();
            return;
        }

        throw new EntityNotFoundException('Cannot find banner for given id');
    }

    /**
     * @param BannerElement $banner
     *
     * @throws ManagerException
     */
    public function save($banner)
    {
        if (!$banner instanceof BannerElement) {
            return;
        }

        if (!$banner->getBannerGroup() instanceof BannerGroup) {
            throw new ManagerException('Missing banner group association');
        }

        $this->em->persist($banner);
        $this->em->flush();
    }
}
