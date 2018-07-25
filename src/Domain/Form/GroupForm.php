<?php declare(strict_types=1);

namespace App\Domain\Form;

use App\Domain\Entity\BannerGroup;

/**
 * @codeCoverageIgnore
 */
class GroupForm
{
    /**
     * @var string
     */
    public $id;

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
        if ($this->id) {
            $group->setId($this->id);
        }

        $group->setActive($this->active);
        $group->setTitle($this->title);
        $group->setDescription($this->description);
    }
}
