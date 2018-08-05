<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Полученная услуга
 * @ORM\Entity()
 */
class Service extends BaseEntity
{
    /**
     * Комментарий
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Сумма денег
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amount;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="services")
     */
    private $client;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="ServiceType")
     */
    private $type;

    public function __toString()
    {
        $type = $this->getType();

        if ($type instanceof ServiceType) {
            return (string)$type->getName();
        }

        return '';
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Service
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Service
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Service
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\ServiceType $type
     *
     * @return Service
     */
    public function setType(ServiceType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\ServiceType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the creation date.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt ? $this->createdAt : new \DateTime();
    }
}
