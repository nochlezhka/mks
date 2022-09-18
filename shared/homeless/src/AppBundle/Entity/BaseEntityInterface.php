<?php

namespace AppBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use DateTime;

interface BaseEntityInterface
{
    public function getId();

    /**
     * Set syncId
     *
     * @param int|null $syncId
     *
     * @return BaseEntityInterface
     */
    public function setSyncId(?int $syncId): BaseEntityInterface;

    /**
     * Get syncId
     *
     * @return integer
     */
    public function getSyncId(): ?int;

    /**
     * Set sort
     *
     * @param int|null $sort
     *
     * @return BaseEntityInterface
     */
    public function setSort(?int $sort): BaseEntityInterface;

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort(): ?int;

    /**
     * Set createdAt
     *
     * @param DateTime|null $createdAt
     *
     * @return BaseEntityInterface
     */
    public function setCreatedAt(DateTime $createdAt = null);

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Set updatedAt
     *
     * @param DateTime|null $updatedAt
     *
     * @return BaseEntity
     */
    public function setUpdatedAt(DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return DateTime
     */
    public function getUpdatedAt();

    /**
     * Set createdBy
     *
     * @param User|null $createdBy
     *
     * @return BaseEntity
     */
    public function setCreatedBy(User $createdBy = null): BaseEntityInterface;

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy(): ?User;

    /**
     * Set updatedBy
     *
     * @param User|null $updatedBy
     *
     * @return BaseEntity
     */
    public function setUpdatedBy(?User $updatedBy = null): BaseEntityInterface;

    /**
     * Get updatedBy
     *
     * @return User
     */
    public function getUpdatedBy(): ?User;
}
