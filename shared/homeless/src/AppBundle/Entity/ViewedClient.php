<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Просмотренная анкета клиента (для истории просмотров)
 * @ORM\Entity()
 */
class ViewedClient extends BaseEntity
{
    /**
     * Клиент
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", onDelete="CASCADE")
    //  * @ORM\ManyToOne(targetEntity="Client", inversedBy="contracts")
     */
    private $client;

    /**
     * Кем создано
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="viewedClients")
     */
    protected $createdBy;

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return ViewedClient
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
