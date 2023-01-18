<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Просмотренная анкета клиента (для истории просмотров)
 */
#[ORM\Entity]
class ViewedClient extends BaseEntity
{
    /**
     * Клиент
     */
    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: "clientViews")]
    private ?Client $client = null;

    /**
     * Кем создано
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "viewedClients")]
    protected ?User $createdBy = null;

    /**
     * Set client
     *
     * @param Client|null $client
     *
     * @return ViewedClient
     */
    public function setClient(Client $client): ViewedClient
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }
}
