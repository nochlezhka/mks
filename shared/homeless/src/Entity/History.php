<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * История просмотров анкет клиентов
 */
#[ORM\Entity]
class History extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Client $client = null;

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
