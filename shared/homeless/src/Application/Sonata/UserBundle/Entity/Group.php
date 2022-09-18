<?php

namespace Application\Sonata\UserBundle\Entity;

use AppBundle\Entity\BaseEntityInterface;
use DateTime;
use Sonata\UserBundle\Entity\BaseGroup as BaseGroup;

/**
 * Группа пользователей
 */
class Group extends BaseGroup implements BaseEntityInterface
{
    /**
     * @var int $id
     */
    protected $id;

    private ?int $syncId;
    private ?int $sort;
    private ?User $createdBy;
    private ?DateTime $createdAt;
    private ?User $updatedBy;
    private ?DateTime $updatedAt;

    private $code;

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set syncId
     *
     * @param int|null $syncId
     *
     * @return Group
     */
    public function setSyncId(?int $syncId): Group
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
     * Set createdAt
     *
     * @param DateTime|null $createdAt
     *
     * @return Group
     */
    public function setCreatedAt(DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param DateTime|null $updatedAt
     *
     * @return Group
     */
    public function setUpdatedAt(DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTime
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
     * @return Group
     */
    public function setCreatedBy(?User $createdBy = null): BaseEntityInterface
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
     * @return Group
     */
    public function setUpdatedBy(?User $updatedBy = null): BaseEntityInterface
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
     * Set code
     *
     * @param string $code
     *
     * @return Group
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set sort
     *
     * @param int|null $sort
     *
     * @return Group
     */
    public function setSort(?int $sort): BaseEntityInterface
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
}
