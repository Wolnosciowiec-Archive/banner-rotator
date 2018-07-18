<?php declare(strict_types=1);

namespace App\Domain\ActionHandler;

use App\Domain\Entity\BannerGroup;
use App\Domain\Repository\GroupRepository;

class GroupListAction
{
    /**
     * @var GroupRepository
     */
    private $repository;

    public function __construct(GroupRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return BannerGroup[]
     */
    public function handle(): array
    {
        return $this->repository->findAll();
    }
}
