<?php declare(strict_types=1);

namespace App\Domain\Form;

use App\Domain\Entity\BannerElement;

/**
 * @codeCoverageIgnore
 */
class BannerForm
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $imageUrl;

    /**
     * @var \DateTime
     */
    public $expiresAt;

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

    /**
     * Rewrites the form to the entity
     *
     * @param BannerElement $element
     * @throws \Exception
     */
    public function mapFormToBanner(BannerElement $element): void
    {
        $element->setUrl($this->url);
        $element->setImageUrl($this->imageUrl);

        if (!$element->getDateAdded()) {
            $element->setDateAdded(new \DateTimeImmutable('now'));
        }

        $element->setExpiresAt($this->expiresAt);
        $element->setActive($this->active);
        $element->setDescription($this->description);
        $element->setTitle($this->title);
    }
}
