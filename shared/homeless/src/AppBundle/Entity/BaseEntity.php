<?php

namespace AppBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use PhpOffice\PhpSpreadsheet\Shared\Date;

abstract class BaseEntity implements BaseEntityInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * Sync id
     * @ORM\Column(type="integer", nullable=true)
     */
    protected ?int $syncId = null;

    /**
     * Сортировка
     * @ORM\Column(type="integer", nullable=true)
     */
    protected ?int $sort = 100;

    /**
     * Когда создано
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $createdAt = null;

    /**
     * Кем создано
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     */
    protected ?User $createdBy = null;

    /**
     * Когда изменено
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $updatedAt = null;

    /**
     * Кем изменено
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     */
    protected User $updatedBy;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set syncId
     *
     * @param int|null $syncId
     *
     * @return BaseEntity
     */
    public function setSyncId(?int $syncId): BaseEntity
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
     * Set sort
     *
     * @param int|null $sort
     *
     * @return BaseEntity
     */
    public function setSort(?int $sort): BaseEntity
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
     * Set the creation date.
     *
     * @param DateTime|null $createdAt
     *
     * @return BaseEntity
     */
    public function setCreatedAt(DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the creation date.
     *
     * @return DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the last update date.
     *
     * @param DateTime|null $updatedAt
     *
     * @return BaseEntity
     */
    public function setUpdatedAt(DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the last update date.
     *
     * @return DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdBy
     *
     * @param User|null $createdBy
     *
     * @return BaseEntity
     */
    public function setCreatedBy(?User $createdBy = null): BaseEntity
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User|null
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
     * @return BaseEntity
     */
    public function setUpdatedBy(?User $updatedBy = null): BaseEntity
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return User
     */
    public function getUpdatedBy(): User
    {
        return $this->updatedBy;
    }
}
