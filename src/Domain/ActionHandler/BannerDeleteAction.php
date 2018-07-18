<?php declare(strict_types=1);

namespace App\Domain\ActionHandler;

use App\Domain\Manager\BannerManager;

class BannerDeleteAction
{
    /**
     * @var BannerManager
     */
    private $manager;

    public function __construct(BannerManager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(string $bannerId): array
    {
        $this->manager->deleteById($bannerId);

        return ['message' => 'OK'];
    }
}
