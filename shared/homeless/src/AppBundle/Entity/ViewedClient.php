<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Sonata\UserBundle\Entity\User;

/**
 * Просмотренная анкета клиента (для истории просмотров)
 * @ORM\Entity()
 */
class ViewedClient extends BaseEntity
{
    /**
     * Клиент
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="clientViews")
     */
    private Client $client;

    /**
     * Кем создано
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="viewedClients")
     * @var User|null
     */
    protected ?User $createdBy = null;

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return ViewedClient
     */
    public function setClient(Client $client = null): ViewedClient
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
