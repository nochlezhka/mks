<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\IntlBundle\Timezone\TimezoneAwareInterface;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;

/**
 * Пользователь (сотрудник)
 * Таблица в старой БД: Worker
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user_user")
 */
class User extends BaseUser implements BaseEntityInterface, TimezoneAwareInterface, LegacyPasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Должность
     * Поле в старой БД: rules
     * @ORM\ManyToOne(targetEntity="Position", inversedBy="users")
     */
    private ?Position $position;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $lastname = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $firstname = null;

    /**
     * Отчество
     * Поле в старой БД: middlename
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $middlename;

    /**
     * Дата доверенности
     * Поле в старой БД: warrantDate
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $proxyDate;

    /**
     * Номер доверенности
     * Поле в старой БД: warrantNum
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $proxyNum;

    /**
     * Паспортные данные
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $passport;

    /**
     * Просмотренные уведомления
     * @ORM\ManyToMany(targetEntity="Notice", inversedBy="viewedBy")
     * @ORM\JoinTable(
     *     name="notice_user",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="notice_id", referencedColumnName="id", unique=true)}
     * )
     */
    private Collection $viewedNotices;

    /**
     * Просмотренные анкеты клиентов
     * @ORM\OneToMany(targetEntity="ViewedClient", mappedBy="createdBy")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private Collection $viewedClients;

    /**
     * Должность текстом
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $positionText;

    /**
     * Таймзона
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $timezone;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $syncId = null;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $sort = null;
    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private ?User $createdBy = null;
    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private ?User $updatedBy = null;

    public function __construct()
    {
        $this->viewedNotices = new ArrayCollection();
        $this->viewedClients = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (empty($this->lastname)) {
            return (string)$this->firstname;
        }

        if (empty($this->firstname)) {
            return (string)$this->lastname;
        }

        return $this->lastname . ' ' . $this->getInitials();
    }

    /**
     * ФИО
     */
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

    /**
     * Иницииалы
     */
    public function getInitials(): string
    {
        $initials = '';

        if (!empty($this->firstname)) {
            $initials = $initials . mb_substr($this->firstname, 0, 1) . '.';
        }

        if (!empty($this->middlename)) {
            $initials = $initials . mb_substr($this->middlename, 0, 1) . '.';
        }

        return $initials;
    }

    /**
     * Set position
     *
     * @param Position|null $position
     *
     * @return User
     */
    public function setPosition(?Position $position = null): User
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return Position
     */
    public function getPosition(): ?Position
    {
        return $this->position;
    }

    /**
     * Set syncId
     *
     * @param int|null $syncId
     *
     * @return User
     */
    public function setSyncId(?int $syncId): User
    {
        $this->syncId = $syncId;

        return $this;
    }

    /**
     * Get syncId
     *
     * @return integer
     */
    public function getSyncId(): ?int
    {
        return $this->syncId;
    }

    /**
     * Set createdBy
     *
     * @param User|null $createdBy
     *
     * @return User
     */
    public function setCreatedBy(?User $createdBy = null): User
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param User|null $updatedBy
     *
     * @return User
     */
    public function setUpdatedBy(?User $updatedBy = null): User
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return User
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * Set middlename
     *
     */
    public function setMiddlename(?string $middlename): User
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    /**
     * Set proxyDate
     *
     */
    public function setProxyDate(?DateTime $proxyDate): User
    {
        $this->proxyDate = $proxyDate;

        return $this;
    }

    /**
     * Get proxyDate
     *
     * @return DateTime
     */
    public function getProxyDate(): ?DateTime
    {
        return $this->proxyDate;
    }

    /**
     * Set proxyNum
     *
     */
    public function setProxyNum(?string $proxyNum): User
    {
        $this->proxyNum = $proxyNum;

        return $this;
    }

    /**
     * Get proxyNum
     *
     * @return string
     */
    public function getProxyNum(): ?string
    {
        return $this->proxyNum;
    }

    /**
     * Set passport
     *
     */
    public function setPassport(?string $passport): User
    {
        $this->passport = $passport;

        return $this;
    }

    /**
     * Get passport
     *
     */
    public function getPassport(): ?string
    {
        return $this->passport;
    }

    /**
     * Set sort
     *
     * @param int|null $sort
     *
     * @return User
     */
    public function setSort(?int $sort): User
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }


    /**
     * Add viewedNotice
     */
    public function addViewedNotice(Notice $viewedNotice): User
    {
        $this->viewedNotices[] = $viewedNotice;

        return $this;
    }

    /**
     * Remove viewedNotice
     */
    public function removeViewedNotice(Notice $viewedNotice)
    {
        $this->viewedNotices->removeElement($viewedNotice);
    }

    /**
     * Get viewedNotices
     *
     * @return Collection
     */
    public function getViewedNotices()
    {
        return $this->viewedNotices;
    }

    /**
     * Add viewedClient
     */
    public function addViewedClient(ViewedClient $viewedClient): User
    {
        $this->viewedClients[] = $viewedClient;

        return $this;
    }

    /**
     * Remove viewedClient
     */
    public function removeViewedClient(ViewedClient $viewedClient)
    {
        $this->viewedClients->removeElement($viewedClient);
    }

    /**
     * Get viewedClients
     *
     * @return Collection
     */
    public function getViewedClients()
    {
        return $this->viewedClients;
    }

    public function isGranted($role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Get positionText
     *
     */
    public function getPositionText(): ?string
    {
        return $this->positionText;
    }

    /**
     * Set positionText
     *
     * @param mixed $positionText
     *
     * @return User
     */
    public function setPositionText($positionText): User
    {
        $this->positionText = $positionText;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }
}
