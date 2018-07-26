<?php declare(strict_types=1);

namespace App\Domain\Form;

use App\Domain\Entity\BannerElement;

/**
 * @codeCoverageIgnore
 */
class NewBannerForm extends BannerForm
{
    /**
     * @var string
     */
    public $id;

    /**
     * Rewrites the form to the entity
     *
     * @param BannerElement $element
     * @throws \Exception
     */
    public function mapFormToBanner(BannerElement $element): void
    {
        $element->setId($this->id);

        parent::mapFormToBanner($element);
    }
}
