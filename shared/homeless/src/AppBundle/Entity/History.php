<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * История просмотров анкет клиентов
 * @ORM\Entity()
 */
class History extends BaseEntity
{
    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client")
     */
    private Client $client;

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return History
     */
    public function setClient(Client $client = null): History
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
}
