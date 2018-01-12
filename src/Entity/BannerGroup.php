<?php declare(strict_types = 1);

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Group is a BannerElement collection
 *
 * Example use case:
 *   - A page which is a anarchosyndicalist-union card wants to have a widget with list of it's sections
 *
 * @see BannerElement
 */
class BannerGroup implements \JsonSerializable
{
    /**
     * @var string $id
     */
    private $id;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var bool $active
     */
    protected $active = false;

    /**
     * @var \DateTimeImmutable $dateAdded
     */
    protected $dateAdded;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var ArrayCollection|BannerElement[] $elements
     */
    protected $elements;
    
    public function __construct()
    {
        $this->dateAdded = new \DateTimeImmutable();
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDateAdded(): ?\DateTimeImmutable
    {
        return $this->dateAdded;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
        ];
    }

    /**
     * @return BannerElement[]|ArrayCollection
     */
    public function getElements()
    {
        return $this->elements;
    }
}
