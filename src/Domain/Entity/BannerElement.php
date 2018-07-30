<?php declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\BannerGroup;

/**
 * A banner that will be displayed
 *
 * @Annotation
 */
class BannerElement implements \JsonSerializable
{
    /**
     * @var string $id
     */
    private $id;

    /**
     * @var string $url
     */
    protected $url;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $imageUrl
     */
    protected $imageUrl;

    /**
     * @var \DateTimeImmutable $dateAdded
     */
    protected $dateAdded;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var BannerGroup $bannerGroup
     */
    protected $bannerGroup;

    /**
     * @var \DateTime|null $expiresAt
     */
    protected $expiresAt;

    /**
     * @var bool $active
     */
    protected $active;

    public function __construct()
    {
        $this->dateAdded = new \DateTimeImmutable();
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url ?? '';
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl ?? '';
    }

    /**
     * @codeCoverageIgnore
     *
     * @return \DateTimeImmutable|null
     */
    public function getDateAdded(): ?\DateTimeImmutable
    {
        return $this->dateAdded;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BannerGroup|null
     */
    public function getBannerGroup(): ?BannerGroup
    {
        return $this->bannerGroup;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return \DateTime|null
     */
    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->active;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $url
     * @return BannerElement
     */
    public function setUrl(string $url): BannerElement
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $imageUrl
     * @return BannerElement
     */
    public function setImageUrl(string $imageUrl): BannerElement
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param \DateTimeImmutable $dateAdded
     *
     * @return BannerElement
     */
    public function setDateAdded(\DateTimeImmutable $dateAdded): BannerElement
    {
        $this->dateAdded = $dateAdded;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $description
     *
     * @return BannerElement
     */
    public function setDescription(string $description): BannerElement
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param BannerGroup $group
     *
     * @return BannerElement
     */
    public function setGroup(BannerGroup $group): BannerElement
    {
        $this->bannerGroup = $group;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param \DateTime|null $expiresAt
     *
     * @return BannerElement
     */
    public function setExpiresAt(?\DateTime $expiresAt): BannerElement
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param bool $active
     *
     * @return BannerElement
     */
    public function setActive(bool $active): BannerElement
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $id
     *
     * @return BannerElement
     */
    public function setId(string $id): BannerElement
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'url'         => $this->getUrl(),
            'imageUrl'    => $this->getImageUrl(),
            'dateAdded'   => $this->getDateAdded() ? $this->getDateAdded()->format('Y-m-d H:i:s') : '',
            'description' => $this->getDescription(),
            'group'       => $this->getBannerGroup() ? $this->getBannerGroup()->getId() : '',
            'expiresAt'   => $this->getExpiresAt() ? $this->getExpiresAt()->format('Y-m-d H:i:s') : '',
        ];
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    /**
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function hasImage(): bool
    {
        return trim($this->imageUrl) !== '';
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $title
     *
     * @return BannerElement
     */
    public function setTitle(string $title): BannerElement
    {
        $this->title = $title;
        return $this;
    }
}
