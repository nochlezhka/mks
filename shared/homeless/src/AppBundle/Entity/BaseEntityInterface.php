<?php

namespace AppBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;

interface BaseEntityInterface
{
    public function getId();

    /**
     * Set syncId
     *
     * @param integer $syncId
     *
     * @return BaseEntity
     */
    public function setSyncId($syncId);

    /**
     * Get syncId
     *
     * @return integer
     */
    public function getSyncId();

    /**
     * Set sort
     *
     * @param integer $sort
     *
     * @return BaseEntity
     */
    public function setSort($sort);

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort();

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return BaseEntity
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return BaseEntity
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Set createdBy
     *
     * @param User $createdBy
     *
     * @return BaseEntity
     */
    public function setCreatedBy(User $createdBy = null);

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy();

    /**
     * Set updatedBy
     *
     * @param User $updatedBy
     *
     * @return BaseEntity
     */
    public function setUpdatedBy(User $updatedBy = null);

    /**
     * Get updatedBy
     *
     * @return User
     */
    public function getUpdatedBy();
}
