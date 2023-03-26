<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Примечание
 */
#[ORM\Entity]
class Note extends BaseEntity
{
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'notes')]
    private ?Client $client = null;

    #[ORM\Column(type: 'boolean')]
    private bool $important = false;

    public function __toString(): string
    {
        return mb_substr(strip_tags($this->text ?? ''), 0, 100);
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function isImportant(): bool
    {
        return $this->important;
    }

    public function setImportant(bool $important): self
    {
        $this->important = $important;

        return $this;
    }

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
