<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Напоминание
 * @ORM\Entity(repositoryClass="App\Repository\NoticeRepository")
 */
class Notice extends BaseEntity
{
    /**
     * Текст
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text = "";

    /**
     * Дата
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $date = null;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client")
     */
    private ?Client $client = null;

    /**
     * Кем просмотрено
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="viewedNotices")
     */
    private Collection $viewedBy;

    public function __toString()
    {
        return mb_substr($this->text, 0, 20) . '...';
    }

    public function __construct()
    {
        $this->viewedBy = new ArrayCollection();
    }

    /**
     * Просмотрено текущим пользователем
     */
    private bool $viewed = false;

    /**
     * @return bool
     */
    public function getViewed(): bool
    {
        return $this->viewed;
    }

    /**
     * @param mixed $viewed
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;
    }

    /**
     * Set text
     *
     * @param string|null $text
     *
     * @return Notice
     */
    public function setText(?string $text): Notice
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Set date
     *
     * @param DateTime|null $date
     *
     * @return Notice
     */
    public function setDate(?DateTime $date): Notice
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return Notice
     */
    public function setClient(Client $client): Notice
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * Add viewedBy
     *
     * @param User $viewedBy
     *
     * @return Notice
     */
    public function addViewedBy(User $viewedBy): Notice
    {
        $this->viewedBy[] = $viewedBy;
        $viewedBy->addViewedNotice($this);

        return $this;
    }

    /**
     * Remove viewedBy
     *
     * @param User $viewedBy
     */
    public function removeViewedBy(User $viewedBy)
    {
        $this->viewedBy->removeElement($viewedBy);
        $viewedBy->removeViewedNotice($this);
    }

    /**
     * Get viewedBy
     *
     * @return Collection
     */
    public function getViewedBy()
    {
        return $this->viewedBy;
    }
}
