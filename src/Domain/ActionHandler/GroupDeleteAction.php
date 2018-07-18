<?php declare(strict_types=1);

namespace App\Domain\ActionHandler;

use App\Domain\Manager\GroupManager;

class GroupDeleteAction
{
    /**
     * @var GroupManager
     */
    private $manager;

    public function __construct(GroupManager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(string $groupId): array
    {
        $this->manager->deleteById($groupId);

        return ['message' => 'OK'];
    }
}
