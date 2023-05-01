<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NoticeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Напоминание
 */
#[ORM\Entity(repositoryClass: NoticeRepository::class)]
class Notice extends BaseEntity
{
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Client $client = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'viewedNotices')]
    private Collection $viewedBy;

    /**
     * Просмотрено текущим пользователем
     */
    private bool $viewed = false;

    public function __construct()
    {
        $this->viewedBy = new ArrayCollection();
    }

    public function __toString(): string
    {
        return mb_substr($this->text ?? '', 0, 20).'...';
    }

    public function isViewed(): bool
    {
        return $this->viewed;
    }

    public function setViewed($viewed): void
    {
        $this->viewed = $viewed;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getViewedBy(): Collection
    {
        return $this->viewedBy;
    }

    public function addViewedBy(User $viewedBy): self
    {
        $this->viewedBy->add($viewedBy);
        $viewedBy->addViewedNotice($this);

        return $this;
    }

    public function removeViewedBy(User $viewedBy): void
    {
        $this->viewedBy->removeElement($viewedBy);
        $viewedBy->removeViewedNotice($this);
    }
}
