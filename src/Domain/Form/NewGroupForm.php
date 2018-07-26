<?php declare(strict_types=1);

namespace App\Domain\Form;

use App\Domain\Entity\BannerGroup;

/**
 * @codeCoverageIgnore
 */
class NewGroupForm extends GroupForm
{
    /**
     * @var string
     */
    public $id;

    public function mapToGroup(BannerGroup $group): void
    {
        $group->setId($this->id);

        parent::mapToGroup($group);
    }
}
