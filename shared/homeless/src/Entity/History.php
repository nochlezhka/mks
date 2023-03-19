<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * История просмотров анкет клиентов
 */
#[ORM\Entity]
class History extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Client::class)]
    private ?Client $client = null;

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client = null): self
    {
        $this->client = $client;

        return $this;
    }
}
