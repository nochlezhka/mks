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
    private $client;

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return History
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
}
