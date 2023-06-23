<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Просмотренная анкета клиента (для истории просмотров)
 */
#[ORM\Entity]
class ViewedClient extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'viewedClients')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    protected ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'clientViews')]
    private ?Client $client = null;

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
