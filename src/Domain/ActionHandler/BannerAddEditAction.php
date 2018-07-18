<?php declare(strict_types=1);

namespace App\Domain\ActionHandler;

use App\Domain\Form\BannerForm;
use App\Domain\Repository\BannerRepository;
use App\Domain\Manager\BannerManager;
use App\Domain\Entity\BannerElement;

/**
 * Banner Edit and Creation
 */
class BannerAddEditAction
{
    /**
     * @var BannerManager
     */
    private $manager;

    /**
     * @var BannerRepository
     */
    private $repository;

    public function __construct(
        BannerManager $manager,
        BannerRepository $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    public function handleEdit(BannerForm $form, string $bannerId): BannerElement
    {
        $banner = $this->repository->assertFindById($bannerId);
        $form->mapFormToBanner($banner);
        $this->manager->storeChanges($banner);

        return $banner;
    }

    public function handleNew(BannerForm $form, string $groupName): BannerElement
    {
        $banner = $this->manager->createForGroup($groupName);
        $form->mapFormToBanner($banner);
        $this->manager->storeChanges($banner);

        return $banner;
    }
}
