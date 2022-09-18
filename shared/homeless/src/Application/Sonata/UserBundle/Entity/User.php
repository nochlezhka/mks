<?php

namespace Application\Sonata\UserBundle\Entity;

use AppBundle\Entity\BaseEntityInterface;
use AppBundle\Entity\Position;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * Пользователь (сотрудник)
 * Таблица в старой БД: Worker
 */
class User extends BaseUser implements BaseEntityInterface
{
    /**
     * Должность
     * Поле в старой БД: rules
     */
    private $position;

    /**
     * Отчество
     * Поле в старой БД: middlename
     */
    private $middlename;

    /**
     * Дата доверенности
     * Поле в старой БД: warrantDate
     */
    private $proxyDate;

    /**
     * Номер доверенности
     * Поле в старой БД: warrantNum
     */
    private $proxyNum;

    /**
     * Паспортные данные
     */
    private $passport;

    /**
     * Просмотренные уведомления
     */
    private $viewedNotices;

    /**
     * Просмотренные анкеты клиентов
     */
    private $viewedClients;

    /**
     * Должность текстом
     */
    private $positionText;

    private ?int $syncId;
    private ?int $sort;
    private ?User $createdBy;
    private ?User $updatedBy;

    public function __construct()
    {
        parent::__construct();
        $this->viewedNotices = new ArrayCollection();
        $this->viewedClients = new ArrayCollection();
    }

    public function __toString()
    {
        if (empty($this->lastname)) {
            return (string)$this->firstname;
        }

        if (empty($this->firstname)) {
            return (string)$this->lastname;
        }

        return (string)($this->lastname . ' ' . $this->getInitials());
    }

    /**
     * ФИО
     */
    public function getFullname()
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
    public function getInitials()
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
     * @param Position $position
     *
     * @return User
     */
    public function setPosition(Position $position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return Position
     */
    public function getPosition()
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
     * @param string $middlename
     *
     * @return User
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set proxyDate
     *
     * @param \DateTime $proxyDate
     *
     * @return User
     */
    public function setProxyDate($proxyDate)
    {
        $this->proxyDate = $proxyDate;

        return $this;
    }

    /**
     * Get proxyDate
     *
     * @return \DateTime
     */
    public function getProxyDate()
    {
        return $this->proxyDate;
    }

    /**
     * Set proxyNum
     *
     * @param string $proxyNum
     *
     * @return User
     */
    public function setProxyNum($proxyNum)
    {
        $this->proxyNum = $proxyNum;

        return $this;
    }

    /**
     * Get proxyNum
     *
     * @return string
     */
    public function getProxyNum()
    {
        return $this->proxyNum;
    }

    /**
     * Set passport
     *
     * @param string $passport
     *
     * @return User
     */
    public function setPassport($passport)
    {
        $this->passport = $passport;

        return $this;
    }

    /**
     * Get passport
     *
     * @return string
     */
    public function getPassport()
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
     *
     * @param \AppBundle\Entity\Notice $viewedNotice
     *
     * @return User
     */
    public function addViewedNotice(\AppBundle\Entity\Notice $viewedNotice)
    {
        $this->viewedNotices[] = $viewedNotice;

        return $this;
    }

    /**
     * Remove viewedNotice
     *
     * @param \AppBundle\Entity\Notice $viewedNotice
     */
    public function removeViewedNotice(\AppBundle\Entity\Notice $viewedNotice)
    {
        $this->viewedNotices->removeElement($viewedNotice);
    }

    /**
     * Get viewedNotices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getViewedNotices()
    {
        return $this->viewedNotices;
    }

    /**
     * Add viewedClient
     *
     * @param \AppBundle\Entity\ViewedClient $viewedClient
     *
     * @return User
     */
    public function addViewedClient(\AppBundle\Entity\ViewedClient $viewedClient)
    {
        $this->viewedClients[] = $viewedClient;

        return $this;
    }

    /**
     * Remove viewedClient
     *
     * @param \AppBundle\Entity\ViewedClient $viewedClient
     */
    public function removeViewedClient(\AppBundle\Entity\ViewedClient $viewedClient)
    {
        $this->viewedClients->removeElement($viewedClient);
    }

    /**
     * Get viewedClients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getViewedClients()
    {
        return $this->viewedClients;
    }

    public function isGranted($role)
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Get positionText
     *
     * @return mixed
     */
    public function getPositionText()
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
    public function setPositionText($positionText)
    {
        $this->positionText = $positionText;

        return $this;
    }
}
