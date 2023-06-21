<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use App\Security\User\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\IntlBundle\Timezone\TimezoneAwareInterface;
use Sonata\UserBundle\Entity\BaseUser;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;

/**
 * Пользователь (сотрудник)
 * Таблица в старой БД: Worker
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'fos_user_user')]
class User extends BaseUser implements BaseEntityInterface, TimezoneAwareInterface, LegacyPasswordAuthenticatedUserInterface
{
    public const ROLE_DEFAULT = Role::EMPLOYEE;

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * Должность
     * Поле в старой БД: rules
     */
    #[ORM\ManyToOne(targetEntity: Position::class, inversedBy: 'users')]
    private ?Position $position;

    #[ORM\Column(type: 'string')]
    private string $lastname = '';

    #[ORM\Column(type: 'string')]
    private string $firstname = '';

    /**
     * Отчество
     * Поле в старой БД: middlename
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $middlename;

    /**
     * Дата доверенности
     * Поле в старой БД: warrantDate
     */
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $proxyDate;

    /**
     * Номер доверенности
     * Поле в старой БД: warrantNum
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $proxyNum;

    /**
     * Паспортные данные
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $passport;

    /**
     * Просмотренные уведомления
     */
    #[ORM\ManyToMany(targetEntity: Notice::class, inversedBy: 'viewedBy')]
    #[ORM\JoinTable(name: 'notice_user')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'notice_id', referencedColumnName: 'id', unique: true)]
    private Collection $viewedNotices;

    /**
     * Просмотренные анкеты клиентов
     */
    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: ViewedClient::class)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $viewedClients;

    /**
     * Должность текстом
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $positionText;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $timezone;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $syncId = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sort = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    public function __construct()
    {
        $this->viewedNotices = new ArrayCollection();
        $this->viewedClients = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (empty($this->lastname)) {
            return $this->firstname ?? '';
        }

        if (empty($this->firstname)) {
            return $this->lastname;
        }

        return $this->lastname.' '.$this->getInitials();
    }

    public function getFullname(): string
    {
        $fullname = [];

        $lastname = $this->getLastname();
        if (!empty($lastname)) {
            $fullname[] = $lastname;
        }

        $firstname = $this->getFirstname();
        if (!empty($firstname)) {
            $fullname[] = $firstname;
        }

        $middlename = $this->getMiddlename();
        if (!empty($middlename)) {
            $fullname[] = $middlename;
        }

        return implode(' ', $fullname);
    }

    public function getInitials(): string
    {
        $initials = '';

        if (!empty($this->firstname)) {
            $initials = $initials.mb_substr($this->firstname, 0, 1).'.';
        }

        if (!empty($this->middlename)) {
            $initials = $initials.mb_substr($this->middlename, 0, 1).'.';
        }

        return $initials;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position = null): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSyncId(): ?int
    {
        return $this->syncId;
    }

    public function setSyncId(?int $syncId): static
    {
        $this->syncId = $syncId;

        return $this;
    }

    public function getCreatedBy(): ?self
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?self $createdBy = null): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?self
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?self $updatedBy = null): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    public function setMiddlename(?string $middlename): self
    {
        $this->middlename = $middlename;

        return $this;
    }

    public function getProxyDate(): ?\DateTimeImmutable
    {
        return $this->proxyDate;
    }

    public function setProxyDate(?\DateTimeImmutable $proxyDate): self
    {
        $this->proxyDate = $proxyDate;

        return $this;
    }

    public function getProxyNum(): ?string
    {
        return $this->proxyNum;
    }

    public function setProxyNum(?string $proxyNum): self
    {
        $this->proxyNum = $proxyNum;

        return $this;
    }

    public function getPassport(): ?string
    {
        return $this->passport;
    }

    public function setPassport(?string $passport): self
    {
        $this->passport = $passport;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function getViewedNotices(): Collection
    {
        return $this->viewedNotices;
    }

    public function addViewedNotice(Notice $viewedNotice): self
    {
        $this->viewedNotices->add($viewedNotice);

        return $this;
    }

    public function removeViewedNotice(Notice $viewedNotice): void
    {
        $this->viewedNotices->removeElement($viewedNotice);
    }

    public function getViewedClients(): Collection
    {
        return $this->viewedClients;
    }

    public function addViewedClient(ViewedClient $viewedClient): self
    {
        $this->viewedClients->add($viewedClient);

        return $this;
    }

    public function removeViewedClient(ViewedClient $viewedClient): void
    {
        $this->viewedClients->removeElement($viewedClient);
    }

    public function isGranted(string $role): bool
    {
        return \in_array($role, $this->getRoles(), true);
    }

    public function getPositionText(): ?string
    {
        return $this->positionText;
    }

    public function setPositionText(?string $positionText): self
    {
        $this->positionText = $positionText;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }
}
