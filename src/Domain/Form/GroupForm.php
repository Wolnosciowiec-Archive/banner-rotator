<?php declare(strict_types=1);

namespace App\Domain\Form;

use App\Domain\Entity\BannerGroup;

/**
 * @codeCoverageIgnore
 */
class GroupForm
{
    /**
     * @var bool
     */
    public $active = true;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    public function mapToGroup(BannerGroup $group): void
    {
        $group->setActive($this->active);
        $group->setTitle($this->title);
        $group->setDescription($this->description);
    }
}
