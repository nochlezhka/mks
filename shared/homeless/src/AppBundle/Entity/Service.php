<?php

namespace AppBundle\Entity;

use DateTime;
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
    private ?string $comment;

    /**
     * Сумма денег
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $amount;

    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="services")
     */
    private Client $client;

    /**
     * Тип
     * @ORM\ManyToOne(targetEntity="ServiceType")
     */
    private ServiceType $type;

    public function __toString()
    {
        if ($this->getType() instanceof ServiceType) {
            return $this->getType()->getName();
        }

        return '';
    }

    /**
     * Set comment
     *
     * @param string|null $comment
     *
     * @return Service
     */
    public function setComment(?string $comment): Service
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set amount
     *
     * @param int|null $amount
     *
     * @return Service
     */
    public function setAmount(?int $amount): Service
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return Service
     */
    public function setClient(Client $client = null): Service
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Set type
     *
     * @param ServiceType|null $type
     *
     * @return Service
     */
    public function setType(ServiceType $type = null): Service
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return ServiceType
     */
    public function getType(): ServiceType
    {
        return $this->type;
    }

    /**
     * Returns the creation date.
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt ?: new DateTime();
    }
}
